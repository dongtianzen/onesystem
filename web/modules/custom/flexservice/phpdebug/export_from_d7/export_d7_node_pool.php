<?php

/**
 *
  from bidash run on lillydash
  require_once('/Applications/AMPPS/www/bidash/modules/custom/phpdebug/export_from_d7/export_d7_node_pool.php');

  drupal_set_time_limit(0);
  _export_d7_node_pool();
 */

function _export_d7_node_pool() {
  $output = array();

  $field_method_collections = _node_pool_method_collections();
  $field_names = $field_method_collections['field_name'];

  $NodeQuery = new NodeQuery();
  // $pool_nids = $NodeQuery->specifyBundleNid('pool');

  $allow_meeting_nids = array(
      3089,
      3147,
      3155,
      3156,
      3252,
      3288,
      3399,
      3457,
      3472,
      3473,
      3484,
      3510,
      5302,
      5309,
      5317,
      5324,
      5384,
      5415,
      5427,
      5431,
      5455,
      5463,
      5464,
      5500,
      16867,
      16875,
      16880,
      16881,
      16892,
      16908,
      16909,
      17017,
      17034,
      17050,
      17056,
      17087,
      17117,
      17128,
      17135,
      17148,
      17158,
      17159,
      17200,
      17201,
      17499,
      17518,
      17531,
      17543,
      17550,
      17555,
      17578,
      17583,
      17598,
      17606,
      17607,
      17628,
      17629,
      17646,
      17650,
      17653,
      17669,
      17684,
      17693,
      17701,
      17721,
      17728,
      17736,
      17740,
      17751,
      17761,
      17771,
      17833,
      18066,
      18122,
      18745,
      18746,
      18762,
      18768,
      18773,
      18778,
      18796,
      18808,
      18832,
      18856,
      18857,
      18858,
      18859,
      18860,
      18861,
      18932,
      18943,
      18959,
      18960,
      18961,
      19017,
      19018,
      19019,
      19020,
      19076,
      19077,
      19078,
      19079,
      19080,
      19082,
      19235,
      19236,
      19262,
      19263,
      19264,
      19265,
      19266,
      19267,
      19268,
      19269,
      19270,
      19383,
      19387,
      19391,
      19394,
      19732,
      19733,
      19734,
      19735,
      19736,
      19767,
      19768,
      19801,
      19824,
      19854,
      19862,
      19869,
      19874,
      19892,
      19897,
      19908,
      19914,
      19929,
      19944,
      19950,
      19956,
      19965,
      19978,
      19991,
      19994,
      20005,
      20012,
      20013,
      20014,
      20060,
      20061,
      20104,
      20113,
      20127,
      20149,
      20175,
      20186,
      20201,
      20213,
      20244,
      20257,
      20266,
      20270,
      20284,
      20304,
      20330,
      20341,
      20358,
      20365,
      20371,
      20380,
      20388,
      20413,
      20465,
      20471,
      20485,
      20673,
      20674,
      20678,
      20687,
      20698,
      20702,
      20711,
      20721,
      20726,
      20731,
      20791,
      20798,
      20799,
      21505,
      21520,
      21529,
      21540,
      21556,
      21574,
      21584,
      21618,
      21622,
      21632,
      21636,
      21645,
      21648,
      21656,
      21667,
      21687,
      21688,
      21698,
      21728,
      21758,
      21768,
      21777,
      21785,
      21790,
      21801,
      21811,
      21818,
      21861,
      21898,
      21909,
      21936,
      21967,
      21997,
      22007,
      22019,
      22031,
      22039,
      22066,
      22103,
      22110,
      22113,
      22118,
      22126,
      22140,
      22153,
      22162,
      22167,
      22173,
      22184,
      22195,
      22204,
      22214,
      22218,
      22228,
      22240,
      22246,
      22258,
      22264,
      22280,
      22298,
      22328,
      22340,
      22347,
      22359,
      22390,
      22670,
      22673,
      22680,
      22688,
      22693,
      22698,
      22706,
      22711,
      22721,
      22729,
      22747,
      22765,
      22779,
      22790,
      23242,
      23243,
      23246,
      23268,
      23274,
      23278,
      23291,
      23293,
      23294,
      23295,
      23296,
      23346,
      23385,
      23405,
      23410,
      23414,
      23426,
      23442,
      23448,
      23454,
      23502,
      23546,
      23650,
      23654,
      23671,
      23682,
      23707,
      23733,
      23744,
      23751,
      23776,
      23795,
      23800,
      23809,
      23825,
      23833,
      23844,
      23854,
      23890,
      23898,
      23913,
      23921,
      23939,
      23952,
      23959,
      23965,
      23989,
      24015,
      24022,
      24034,
      24039,
      24052,
      24062,
      24065,
      24074,
      24756,
      24774,
      24855,
      24883,
      24908,
      24955,
      24969,
      24985,
      25120,
      25128,
      25133,
      25140,
      25147,
      25173,
      25191,
      25199,
      25205,
      25230,
      25298,
      25334,
      25368,
      25382,
      25390,
      25405,
      25435,
      25447,
      25452,
      25460,
      25467,
      25475,
      25491,
      25746,
      25765,
      25774,
      25780,
      25785,
      25805,
      25815,
      25820,
      25826,
      25831,
      25844,
      25849,
      25858,
      25864,
      25885,
      25909,
      25918,
      25922,
      25942,
      25952,
      25960,
      25965,
      25974,
      25981,
      25989,
      25995,
      26012,
      26023,
      26281,
      26290,
      26299,
      26307,
      26311,
      26314,
      26331,
      26340,
      26355,
      26368,
      26375,
      26384,
      26392,
      26425,
      26432,
      26525,
      26528,
      26529,
      26537,
      26548,
      26554,
      26563,
      26569,
      26575,
      26585,
      26589,
      26684,
      26770,
      26773,
      26774,
      26840,
      26847,
      26853,
      26857,
      26865,
      26884,
      26898,
      26904,
      26915,
      26926,
      26948,
      26949,
      27112,
      27113,
      27115,
      27117,
      27153,
      27160,
      27161,
      27297,
      27314,
      27319,
      27328,
      27333,
      27336,
      27342,
      27372,
      27414,
      27428,
      27449,
      27463,
      27470,
      27478,
      27481,
      27484,
      27496,
      27517,
      27532,
      27550,
      27554,
      27675,
      27688,
      27698,
      27704,
      27712,
      27731,
      27734,
      27773,
      27812,
      27818,
      27826,
      27836,
      27848,
      27867,
      27886,
      27906,
      27913,
      27920,
      27930,
      27958,
      27973,
      27978,
      27990,
      27998,
      28002,
      28007,
      28037,
      28044,
      28053,
      28058,
      28062,
      28082,
      28089,
      28114,
      28125,
      28130,
      28139,
      28144,
      28152,
      28164,
      28170,
      28179,
      28220,
      28233,
      28240,
      28246,
      28261,
      28266,
      28271,
      28277,
      28290,
      28295,
      28301,
      28308,
      28340,
      28344,
      28812,
      28818,
      28833,
      28836,
      28844,
      28867,
      28870,
      28871,
      28875,
      28881,
      28889,
      28907,
      28912,
      28920,
      28925,
      28934,
      28948,
      28966,
      28973,
      28984,
      29001,
      29008,
      29015,
      29020,
      29024,
      29031,
      29038,
      29043,
      29049,
      29064,
      29069,
      29074,
      29079,
      29086,
      29090,
      29096,
      29102,
      29112,
      29119,
      29144,
      29150,
      29163,
      29169,
      29184,
      29189,
      29196,
      29202,
      29206,
      29210,
      29214,
      29222,
      29230,
      29235,
      29241,
      29246,
      29254,
      29259,
      29264,
      29267,
      29277,
      29285,
      29290,
      24867,
      28018,
  );

  $allow_pool_nids = $NodeQuery->poolNidsByMeetingNids($allow_meeting_nids);

  dpm(count($allow_pool_nids));

  $pool_nodes = node_load_multiple($allow_pool_nids);

  foreach ($pool_nodes as $key => $node) {
    // if ($key > 26) {
    //   continue;
    // }

    $PoolInfo = new PoolInfo($node->nid);

    // if ($node->created < 1489536000) {  // (your time zone): 3/14/2017, 8:00:00 PM
    //   continue;
    // }

    foreach ($field_names as $field_name => $row) {
      // first set pool nid self
      $output[$node->nid]['field_pool_poolnid'] = $node->nid;

      $field_value = array();
      $field_info = field_info_field($row['d7_field_name']);

      if ($field_info['type'] == 'entityreference') {
        if (isset($node->{$row['d7_field_name']}['und'][0]['target_id'])) {
          // only meeting nid
          $output[$node->nid][$row['d8_field_name']] = $node->{$row['d7_field_name']}['und'][0]['target_id'];
        }
      }
      else {       // text field
        if (isset($node->{$row['d7_field_name']}['und'][0]['value'])) {
          $user_uid = NULL;
          $question_key = $node->{$row['d7_field_name']}['und'][0]['value'];


          $pieces = explode("-", $question_key);

          if ($pieces[0] == 8) {
            dpm('not right question_tid - for this - ' . $question_key . ' on pool - ' . $node->nid);
          }

          $question_tid = $pieces[0];
          if ($question_tid > 8000000) {
            $question_tid = 8;
            $user_uid = $pieces[0] - 8000000;
          }

          $type_tid = $pieces[1];
          $question_term = taxonomy_term_load($question_tid);
          if (isset($question_term->name)) {
            $output[$node->nid]['field_pool_answer'][$question_key]['field_pool_questionname'] = $question_term->name;
          }
          else {
            dpm('not found question_tid - for this - ' . $question_key . ' on pool - ' . $node->nid);
          }

          // user answer
          if ($user_uid) {
            $user = user_load($user_uid);
            if (isset($user->name)) {
              $output[$node->nid]['field_pool_answer'][$question_key]['field_pool_username'] = $user->name;
            }
            else {
              dpm('not found user_uid - ' . $user_uid . ' for this - ' . $pieces[0] . ' on pool - ' . $node->nid);
            }
          }

          // field type
          $type_term = taxonomy_term_load($type_tid);
          if (isset($type_term->name)) {
            $output[$node->nid]['field_pool_answer'][$question_key]['field_pool_questiontype'] = $type_term->name;
          }
          else {
            dpm('not found question type tid - for this - ' . $question_key . ' on pool - ' . $node->nid);
          }

          // answer
          foreach ($node->{$row['d7_field_name']}['und'] as $key => $value) {
            if ($key > 0) {
              if ($type_term->name == 'selectkey') {
                $selectkey_term = taxonomy_term_load($value['value']);
                if ($selectkey_term) {
                  $output[$node->nid]['field_pool_answer'][$question_key]['field_pool_selectkey_tid'][] = $value['value'];
                  $output[$node->nid]['field_pool_answer'][$question_key]['answer'][] = $selectkey_term->name;
                }
                else {
                  // dpm($value);
                  dpm('not found selectkey value - ' . $value . ' on pool - ' . $node->nid);
                }
              }
              else {
                $output[$node->nid]['field_pool_answer'][$question_key]['answer'][] = $value['value'];
              }
            }
          }
        }
      }

    }
  }

  $json_data = json_encode($output, JSON_UNESCAPED_UNICODE);
  dpm($json_data);

}

/**
 *
 */
function _node_pool_method_collections() {
  $output['field_name'] = array(
    array(
      'd7_field_name' => 'field_pool_meeting_nid',
      'd8_field_name' => 'field_pool_meetingnid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_aa',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_ab',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_ac',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_ad',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_ae',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_af',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_ag',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_ah',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_ai',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_aj',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_ak',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_al',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_am',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_an',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_ao',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_ap',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_aq',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_ar',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_as',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_at',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_au',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_av',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_aw',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_ax',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_ay',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_az',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_ba',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_bb',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_bc',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_bd',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_be',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_bf',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_bg',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_bh',
      'd8_field_name' => 'field_pool_questiontid',
    ),
    array(
      'd7_field_name' => 'field_pool_answer_bi',
      'd8_field_name' => 'field_pool_questiontid',
    ),
  );

  return $output;
}
