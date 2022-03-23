<?php

namespace Drupal\eresources;

/**
 * Encapsulates license data.
 */
class License {

  /**
   * License data from the API.
   *
   * @var StdClass
   */
  private $licenseData;

  /**
   * Gathered notes.
   *
   * @var array
   */
  private $notes;

  /**
   * Gathered Methods_Supported.
   *
   * @var array
   */
  private $methodsSupported;

  /**
   * Class constructor.
   */
  public function __construct($licenseData) {
    $this->licenseData = $licenseData;
    if (property_exists($licenseData, 'content')) {
      $this->licenseData = $licenseData->content;
    }
    $this->notes = $this->getNotes();
    $this->methodsSupported = $this->getMethodsSupported();
  }

  /**
   * Converts a given value to TRUE or FALSE.
   */
  private function booleanify($value) {
    $filtered = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    // Assume false for any non-true value.
    return $filtered === TRUE ? TRUE : FALSE;
  }

  /**
   * Gets the name data.
   */
  public function getName() {
    return $this->licenseData->name;
  }

  /**
   * Interprets parts of the license and figures out the permissions to display.
   */
  public function getPermissions() {
    // Concurrent users.
    $term = $this->getTerm('Restrict_Concurrent_Users');
    $subTerm = $this->getSubTerm($term, 'Restrictions');
    $option = $this->getOption($subTerm, 'Concurrent_Users');
    $unlimitedConcurrentUsers = !$this->booleanify($this->getOptionValue($option));

    // Remote access.
    $remoteAccess = $this->booleanify($this->getTermValue('Remote_Access'));

    // ILL.
    $ill = $this->booleanify($this->getTermValue('Interlibrary_Loan'));

    // Distance Education.
    $distanceEd = $this->booleanify($this->getTermValue('Distance_Education'));

    // Perpetual Access.
    $perpetual = $this->booleanify($this->getTermValue('Post-Cancellation_Access'));

    // Academc Sharing.
    $term = $this->getTerm('Copying_and_Sharing');
    $subTerm = $this->getSubTerm($term, 'Additional_Rights_And_Restrictions');
    $option = $this->getOption($subTerm, 'Sharing_for_Academic_Purposes');
    $academicSharing = $this->booleanify($this->getOptionValue($option));

    // Legal Jurisdiction.
    $term = $this->getTerm('Governing_Jurisdiction');
    $subTerm = $this->getSubTerm($term, 'Country_of_Jurisdiction');
    $option = $this->getOption($subTerm, 'Country_Code');
    $jurisdiction = $this->getOptionValue($option);

    // Authorized Users (Faculty, Student, Alumni & Walk-ins).
    $term = $this->getTerm('Authorized_Users');
    $subTerm = $this->getSubTerm($term, 'User_Type');

    $facultyOption = $this->getOption($subTerm, 'Faculty');
    $faculty = $this->booleanify($this->getOptionValue($facultyOption));

    $studentsOption = $this->getOption($subTerm, 'Student');
    $students = $this->booleanify($this->getOptionValue($studentsOption));

    $alumniOption = $this->getOption($subTerm, 'Alumni');
    $alumni = $this->booleanify($this->getOptionValue($alumniOption));

    $walkinsOption = $this->getOption($subTerm, 'Walk-ins');
    $walkins = $this->booleanify($this->getOptionValue($walkinsOption));

    // Electronic Linking.
    $electronicLinking = $this->booleanify($this->getTermValue('Electronic_Linking'));

    // Images.
    $imagesOption = $this->getCustomTerm('Use of Images');
    $images = FALSE;
    if ($imagesOption !== NULL) {
      $images = $this->booleanify($imagesOption->termValue);
    }

    $permissions = [
       // Public.
      'authorized-users' => [
        'name' => 'Research and Study for Authorized Users',
        'status' => ($faculty && $students) ? TRUE : FALSE,
        'note' => 'Authorized_Users/Other_Users',
        'default-note' => 'Includes downloading and/or printing materials for personal academic use by members of the UNB community.',
      ],
      'class-handouts'  => [
        'name' => 'Print for class handouts',
        'status' => $this->getMethodSupported('Print_Copy'),
        'note' => 'Print_Copy/Description',
        'default-note' => 'Includes printing and distributing multiple copies of materials to students in classes at UNB.',
      ],
      'distribute-a-link' => [
        'name' => 'Distribute a link or persistent URL',
        'status' => $electronicLinking,
        'note' => 'Electronic_Linking/Note',
        'default-note' => 'Includes providing electronic links to licensed materials for access by students, faculty, and staff at UNB.',
      ],
      'distribute-a-pdf' => [
        'name' => 'Distribute a PDF through D2L or Course Reserves',
        'status' => $this->getMethodSupported('Electronic_Course_Materials'),
        'note' => 'Course_Materials',
        'default-note' => "Includes uploading an electronic copy of materials for class distribution through D2L or UNB Libraries' Course Reserves.",
      ],
      'use-images' => [
        'name' => 'Use images for academic purposes',
        'status' => $images,
        'note' => 'Use of Images',
        'default-note' => 'Includes the use of images (including charts, graphs, tables, graphics, etc.) from materials for use in classes at UNB.',
      ],
      'simultaneous-users' => [
        'name' => 'Unlimited simultaneous users',
        'status' => $unlimitedConcurrentUsers,
        'note' => 'Restrict_Concurrent_Users/Note',
        'default-note' => 'Simultaneous access by unlimited multiple users permitted.',
      ],

       // Staff.
      'off-campus' => [
        'name' => 'Off-Campus Access (Proxy)',
        'status' => $remoteAccess,
        'note' => 'Remote_Access/Note',
      ],
      'printed-course-packs' => [
        'name' => 'Printed Course Packs',
        'status' => $this->getMethodSupported('Printed_Course_Pack'),
        'note' => 'Printed_Course_Pack/Description',
      ],
      'interlibrary-loan' => [
        'name' => 'Interlibrary Loan',
        'status' => $ill,
        'note' => 'Interlibrary_Loan/Note',
      ],
      'distance-education' => [
        'name' => 'Distance Education',
        'status' => $distanceEd,
        'note' => 'Distance_Education/Note',
      ],
      'perpetual-access' => [
        'name' => 'Perpetual Access',
        'status' => $perpetual,
        'note' => 'Post-Cancellation_Access/Note',
      ],
      'alumni' => [
        'name' => 'Alumni',
        'status' => $alumni,
        'note' => 'Authorized_Users',
      ],
      'walk-ins' => [
        'name' => 'Walk-ins',
        'status' => $walkins,
        'note' => 'Authorized_Users',
      ],
      'copyright-and-sharing' => [
        'name' => 'Sharing with Non-UNB Colleagues',
        'status' => $academicSharing,
        'note' => 'Copying_and_Sharing',
      ],
      'legal' => [
        'name' => 'Legal',
        'status' => -1,
        'default-note' =>
        'Legal Jurisdiction: ' . $jurisdiction . '<br>Applicable Copyright Law: ' . $this->getTermValue('Applicable_Copyright_Law'),
      ],
    ];

    return $permissions;
  }

  /**
   * Gets a note.
   */
  public function getNote($termName) {
    if (array_key_exists($termName, $this->notes)) {
      // Remove staff-only comments.
      return preg_replace('/\[INTERNAL NOTE\].*$/', '', $this->notes[$termName]);
    }
    return '';
  }

  /**
   * Gets all notes.
   */
  private function getNotes() {
    $notes = [];
    $terms = $this->getTerm(NULL);
    foreach ($terms as $term) {
      if (!property_exists($term, 'type')) {
        continue;
      }

      $key = $term->type;
      if ($key == 'Custom Term') {
        $key = $term->name;
      }

      // Special terms.
      if ($key == 'Use of Images') {
        $notes[$key] = $term->description;
        continue;
      }

      // Some notes are contained in $term->notes.
      if (property_exists($term, 'notes')) {
        $noteValues = [];
        foreach ($term->notes->note as $note) {
          $noteValues[] = $note->note;
        }
        $notes[$key] = implode('<br>', $noteValues);
      }

      // Some notes are contained in $term->subTerms->subTerms.
      $subterm = $this->getSubTerm($term, 'Note');
      $options = $this->getOption($subterm, NULL);
      if ($options !== NULL) {
        foreach ($options as $note) {
          $notes["$key/" . $subterm->subTermName] = $this->getOptionValue($note);
        }
      }

      // Search sub-terms for notes.
      $subterms = $this->getSubTerm($term, NULL);
      foreach ($subterms as $subterm) {
        if ($subterm->subTermName == 'Note') {
          continue;
        }
        $note = $this->getOption($subterm, 'Note');
        if ($note !== NULL) {
          $notes["$key/" . $subterm->subTermName] = $note->value ?? '';
        }
        else {
          // Search for "Description" metadata.
          $options = $this->getOption($subterm, NULL);
          foreach ($options as $option) {
            if (property_exists($option, 'optionMetadata')) {
              foreach ($option->optionMetadata->metadata as $metadata) {
                if ($metadata->name == 'Description' && !empty($metadata->value)) {
                  $notes[$option->name . '/Description'] = $metadata->value;
                }
              }
            }
          }
        }
      }
    }
    return $notes;
  }

  /**
   * Gets all Methods_Supported.
   */
  private function getMethodsSupported() {
    $methodsSupported = [];
    $terms = $this->getTerm(NULL);
    foreach ($terms as $term) {
      $subTerm = $this->getSubTerm($term, 'Methods_Supported');
      $options = $this->getOption($subTerm, NULL);
      if ($options === NULL) {
        continue;
      }
      foreach ($options as $method) {
        $value = $this->getOptionValue($method);
        $methodsSupported[$method->name] = $value == 'true' ? TRUE : FALSE;
      }
    }
    return $methodsSupported;
  }

  /**
   * Gets a single Method_Supported.
   */
  private function getMethodSupported($termName) {
    if (array_key_exists($termName, $this->methodsSupported)) {
      return $this->methodsSupported[$termName];
    }
    return FALSE;
  }

  /**
   * Gets a specific term, or all terms.
   */
  private function getTerm($termName) {
    $terms = [];
    if (property_exists($this->licenseData, 'terms') && property_exists($this->licenseData->terms, 'term')) {
      foreach ($this->licenseData->terms->term as $term) {
        $terms[] = $term;
        if (property_exists($term, 'type')) {
          if ($termName && $term->type == $termName) {
            return $term;
          }
        }
      }
    }
    return $termName == NULL ? $terms : NULL;
  }

  /**
   * Get a specific custom term or all custom terms.
   */
  private function getCustomTerm($termName) {
    $terms = [];
    if (property_exists($this->licenseData, 'terms') && property_exists($this->licenseData->terms, 'term')) {
      foreach ($this->licenseData->terms->term as $term) {
        $terms[] = $term;
        if (property_exists($term, 'name')) {
          if ($termName && $term->name == $termName) {
            return $term;
          }
        }
      }
    }
    return $termName == NULL ? $terms : NULL;
  }

  /**
   * Convenience method to get a term value.
   */
  private function getTermValue($term_type) {
    $term = $this->getTerm($term_type);
    if ($term !== NULL && property_exists($term, 'termValue')) {
      return $term->termValue;
    }
    return '';
  }

  /**
   * Gets a specific sub term or all sub terms.
   */
  private function getSubTerm($term, $subTermName) {
    if ($term == NULL) {
      return NULL;
    }
    $subTerms = [];
    if (property_exists($term, 'subTerms') && property_exists($term->subTerms, 'subTerm')) {
      foreach ($term->subTerms->subTerm as $subTerm) {
        $subTerms[] = $subTerm;
        if ($subTermName && property_exists($subTerm, 'subTermName') && $subTerm->subTermName == $subTermName) {
          return $subTerm;
        }
      }
    }
    return $subTermName == NULL ? $subTerms : NULL;
  }

  /**
   * Gets a specific option or all options.
   */
  private function getOption($subTerm, $optionName) {
    if ($subTerm == NULL) {
      return NULL;
    }
    $options = [];
    if (property_exists($subTerm, 'options') && property_exists($subTerm->options, 'option')) {
      foreach ($subTerm->options->option as $option) {
        $options[] = $option;
        if ($optionName && property_exists($option, 'name') && $option->name == $optionName) {
          return $option;
        }
      }
    }
    return $optionName == NULL ? $options : NULL;
  }

  /**
   * Convenience method to get an option value.
   */
  private function getOptionValue($option) {
    if ($option == NULL) {
      return '';
    }
    if (property_exists($option, 'value') && $option->value != 'null') {
      return $option->value;
    }
    return '';
  }

}
