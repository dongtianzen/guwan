<?php

/**
 * @file
 */
namespace Drupal\dashjson\Content;

/**
 * An example controller.
 $DashjsonVueTerm = new DashjsonVueTerm();
 $DashjsonVueTerm->demoPage();
 */
class DashjsonVueTerm extends ControllerBase {

  /**
   *
   */
  public function basicTermData($vid = NULL) {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName($vid);

    foreach ($terms as $term) {
      $tbody[] = [
        $term->getName(),
        $term->getDescription(),
        '<a href=' . base_path() . 'taxonomy/term/' . $term->id() . '/edit' . '>Edit</a>',
      ];
    }

    $output = array(
      "thead" => [
        [
          "NAME",
          "DESCRIPTION",
          "EDIT"
        ]
      ],
      "tbody" => $tbody
    );

    return $output;
  }

}
