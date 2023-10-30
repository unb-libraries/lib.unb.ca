# Drupal Footnotes CKEditor Plugin
- Used to create automatically numbered footnotes within a text
- Suppprts CKEditor module on Drupal 7+
- Use **3.1.x** branch for D9/D10
- https://www.drupal.org/project/footnotes

## Dependencies
- CKEditor 4.x FakeObjects module<br>
    https://www.drupal.org/project/fakeobjects
- Library Plugin<br>
  https://ckeditor.com/cke4/addon/fakeobjects
  - UNB Libraries GitHub Mirror:<br>
    https://github.com/unb-libraries/upstream-library-mirror
    <pre>
    {
      "package": {
        "dist": {
          "type": "zip",
          "url": "https://github.com/unb-libraries/upstream-library-mirror/raw/master/fakeobjects/fakeobjects_4.23.0-lts.zip"
        },
        "name": "ckeditor/fakeobjects",
        "type": "drupal-library",
        "version": "4.23.0"
      },
      "type": "package"
    }
    </pre>
  
 ## Patches
- Outstanding issue (Oct 2023):<br>
  Warnings are generated for empty footnotes with empty value
  (https://www.drupal.org/project/footnotes/issues/3381366)
  - WIP Patch (#14):<br>
    https://www.drupal.org/files/issues/2023-08-23/fix-warnings-when-leaving-values-empty-3381366-5_0.patch