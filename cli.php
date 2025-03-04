<?php

$arguments = $argv;
unset($arguments[0]);

if(isset($arguments[1])){
    if($arguments[1] != 'github-activity'){
        echo "Comando Inválido, tente github-activity \n";
    }else{
        print_r(getUsername($arguments[2])) ;
    }
}

function getUsername($username){
    $endpoint = "https://api.github.com/users/$username/events";
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json','User-Agent: github-user-activity'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        var_dump($error_msg);
        echo "Error: $error_msg\n";
    }else{
        $responseArray = json_decode($response, true);
        curl_close($ch);
        return parseResponse($responseArray);
    }
}

function parseResponse($responseArray){
    echo "Output: \n";
    foreach($responseArray as $key => $gitActivity){
     if($gitActivity['type'] == 'PushEvent'){
         echo "- Pushed "
         .$gitActivity['payload']['size']." commits to "
         .$gitActivity['repo']['name'].PHP_EOL;
         }
     elseif ($gitActivity['type'] == 'CreateEvent'){
         echo "- Created new ".$gitActivity['payload']['ref_type']. " on "
         .$gitActivity['repo']['name'].PHP_EOL;
        }
     elseif ($gitActivity['type'] == 'IssueCommentEvent') {
         if ($gitActivity['payload']['action'] == 'created') {
             echo "- " . ucfirst($gitActivity['payload']['action']) . " new issue comment on " .
                 $gitActivity['repo']['name'] . PHP_EOL;
         } else {
             echo "- " . ucfirst($gitActivity['payload']['action']) . " already existing issue on " .
                 $gitActivity['repo']['name'] . PHP_EOL;
         }
     }
     elseif($gitActivity['type'] == 'IssuesEvent'){
            echo "- " . ucfirst($gitActivity['payload']['action']). " issue ".
            "on " . $gitActivity['repo']['name'].PHP_EOL;
         }
     elseif($gitActivity['type'] == 'PullRequestEvent'){
         echo "- " . ucfirst($gitActivity['payload']['action']). " pull request ".
          "on " . $gitActivity['repo']['name'].PHP_EOL;
     }
     elseif($gitActivity['type'] == 'WatchEvent'){
         echo "- Starred " . $gitActivity['repo']['name'] . PHP_EOL;
     }
     }
}
//MUDAR PARA SWITCH
//https://docs.github.com/en/rest/using-the-rest-api/github-event-types?apiVersion=2022-11-28#issuesevent
//IssueCommentEvent
//IssuesEvent
//PullRequestEvent
//PushEvent
//WatchEvent