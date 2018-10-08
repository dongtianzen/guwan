<?php

$end_date = '2018-09-30';

    $previous_entitys = \Drupal::getContainer()
      ->get('baseinfo.querynode.service')
      ->queryDayNodesByCodeByQueryRange($tid = 1837, $end_date, $range_num = 42);


dpm(count($previous_entitys));

    $output = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldFirstValueCollection($previous_entitys, 'field_day_close');


dpm($output);


    $output = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldFirstValueCollection($previous_entitys, 'field_day_date');

dpm($output);
