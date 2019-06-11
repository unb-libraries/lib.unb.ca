<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Research Subjects' block for the UNB Libraries front page.
 *
 * @Block(
 *  id = "research_subjects",
 *  admin_label = @Translation("UNB Libraries Research Subjects"),
 *   category = @Translation("UNB Libraries"),
 * )
 */
class ResearchSubjects extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $html =
      '<h2>Research by Subject</h2> 
      <div class="d-flex flex-wrap">
        <ul class="flex-grow-1 mb-0">
          <li><a href="//guides.lib.unb.ca/category/anthropology">Anthropology</a></li>
          <li><a href="//guides.lib.unb.ca/category/biology">Biology</a></li>
          <li><a href="//guides.lib.unb.ca/category/business">Business Administration</a></li>
          <li><a href="//guides.lib.unb.ca/category/chemistry">Chemistry</a></li>
          <li><a href="//guides.lib.unb.ca/category/classics">Classics and Ancient History</a></li>
          <li><a href="//guides.lib.unb.ca/category/ics">Communication Studies</a></li>
          <li><a href="//guides.lib.unb.ca/category/computerscience">Computer Science</a></li>
          <li><a href="//guides.lib.unb.ca/category/criminology">Criminology</a></li>
          <li><a href="//guides.lib.unb.ca/category/earth-sciences">Earth Sciences</a></li>
          <li><a href="//guides.lib.unb.ca/category/economics">Economics</a></li>
          <li><a href="//guides.lib.unb.ca/category/education">Education</a></li>
          <li><a href="//guides.lib.unb.ca/category/engineering">Engineering</a></li>
          <li><a href="//guides.lib.unb.ca/category/english">English</a></li>
          <li><a href="//guides.lib.unb.ca/category/entrepreneurship">Entrepreneurship</a></li>
        </ul>
        <ul class="flex-grow-1 mb-0">
          <li><a href="//guides.lib.unb.ca/category/familyviolence">Family Violence Issues</a></li>
          <li><a href="//guides.lib.unb.ca/category/film">Film Studies</a></li>
          <li><a href="//guides.lib.unb.ca/category/finearts">Fine Arts</a></li>
          <li><a href="//guides.lib.unb.ca/category/forestry">Forestry &amp; Environmental Management</a></li>
          <li><a href="//guides.lib.unb.ca/category/french">French</a></li>
          <li><a href="//guides.lib.unb.ca/category/womensstudies">Gender and Women\'s Studies</a></li>
          <li><a href="//guides.lib.unb.ca/category/german">German Literature</a></li>
          <li><a href="//guides.lib.unb.ca/category/gerontology">Gerontology</a></li>
          <li><a href="//guides.lib.unb.ca/category/history">History</a></li>
          <li><a href="//guides.lib.unb.ca/category/development">International Development Studies</a></li>
          <li><a href="//guides.lib.unb.ca/category/journalism">Journalism &amp; Communications</a></li>
          <li><a href="//guides.lib.unb.ca/category/kinesiology">Kinesiology</a></li>
          <li><a href="//guides.lib.unb.ca/category/law">Law</a></li>
          <li><a href="//guides.lib.unb.ca/category/math">Mathematics</a></li>
        </ul>
        <ul class="flex-grow-1">
          <li><a href="//guides.lib.unb.ca/category/media">Media Arts and Culture</a></li>
          <li><a href="//guides.lib.unb.ca/category/music">Music</a></li>
          <li><a href="//guides.lib.unb.ca/category/nativestudies">Native Studies</a></li>
          <li><a href="//guides.lib.unb.ca/category/nursing">Nursing &amp; Health Sciences</a></li>
          <li><a href="//guides.lib.unb.ca/category/philosophy">Philosophy</a></li>
          <li><a href="//guides.lib.unb.ca/category/physics">Physics</a></li>
          <li><a href="//guides.lib.unb.ca/category/politicalscience">Political Science</a></li>
          <li><a href="//guides.lib.unb.ca/category/psychology">Psychology</a></li>
          <li><a href="//guides.lib.unb.ca/category/religiousstudies">Religious Studies</a></li>
          <li><a href="//guides.lib.unb.ca/category/sts">Science &amp; Technology Studies</a></li>
          <li><a href="//guides.lib.unb.ca/category/socialwork">Social Work</a></li>
          <li><a href="//guides.lib.unb.ca/category/sociology">Sociology</a></li>
          <li><a href="//guides.lib.unb.ca/category/spanish">Spanish Literature</a></li>
          <li class="list-unstyled mt-2">
            <a href="//guides.lib.unb.ca/research-guides">
              <span class="fa-plus-circle fas"></span>&nbsp;All Subject and Course Guides
            </a>
          </li>
        </ul>
      </div>';

    $render_array['wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => [
          'research-subjects',
        ],
        'class' => [
          'mt-5',
          'p-0',
        ],
      ],
    ];
    $render_array['wrapper']['children'] = [
      '#type' => 'markup',
      '#markup' => $html,
    ];

    return $render_array;
  }

}
