<?php
ini_set("display_errors", "1");
ini_set("display_startup_errors", "1");
ini_set('error_reporting', E_ALL);

//AUTH Б24
$domain = '******.bitrix24.ru';
$auth = '*********';
$user = '1';

$count = 0;

$listGet = executeREST(
    'lists.element.get',
    array(
        'IBLOCK_TYPE_ID' => 'lists',
        'IBLOCK_ID' => 25,
        'start' => -1,
        'ELEMENT_ORDER' => array(
            "ID" => "DESC",
        ),
        'FILTER' => array(

        ),
    ),
    $domain, $auth, $user);

$total = count($listGet['result']);

while ($count != $total) {
    $idOfList = $listGet['result'][$count]['ID'];
    $values = $listGet['result'][$count]['PROPERTY_107'];

    $count++;

    foreach ($values as $value);

    $userGet = executeREST(
        'user.get',
        array(
            'order' => array(
                'ID' => 'DESC',
            ),
            'FILTER' => array(
                'EMAIL' => $value,
            ),
        ),
        $domain, $auth, $user);

    $userId = $userGet['result'][0]['ID'];

    if (!empty($userId)) {
        $startworkflow = executeREST(
            'bizproc.workflow.start',
            array(
                'TEMPLATE_ID' => '31',
                'DOCUMENT_ID' => array(
                    'lists', 'Bitrix\Lists\BizprocDocumentLists', $idOfList,
                ),
                'PARAMETERS' => array(
                    'user_emploee' => 'user_' . $userId,
                ),
            ),
            $domain, $auth, $user);
    } else {

    }
}

function executeREST($method, array $params, $domain, $auth, $user)
{
    $queryUrl = 'https://' . $domain . '/rest/' . $user . '/' . $auth . '/' . $method . '.json';
    $queryData = http_build_query($params);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $queryUrl,
        CURLOPT_POSTFIELDS => $queryData,
    ));
    return json_decode(curl_exec($curl), true);
    curl_close($curl);
}

function writeToLog($data, $title = '')
{
    $log = "\n------------------------\n";
    $log .= date("Y.m.d G:i:s") . "\n";
    $log .= (strlen($title) > 0 ? $title : 'DEBUG') . "\n";
    $log .= print_r($data, 1);
    $log .= "\n------------------------\n";
    file_put_contents(getcwd() . '/generateconto.log', $log, FILE_APPEND);
    return true;
}
