<?php

//Configuration
$hubspotApiKey = "REPLACE_WITH_YOUR_HUBSPOT_API_KEY";

$wordsToAvoid = array(
  "free",
  "help",
  "reminder",
  "cancelled",
  "Re:",
  "Fwd:",
  "Fw:"
);

$phrasesToAvoid = array(
  "percent off"
);

$charactersToAvoid = array(
  "%",
  "$",
  "!"
);

?>

<!DOCTYPE html>
<html>

<head>
<title>Email Subject Line Tester</title>
</head>

<body>
  
<h1>Email Subject Line Tester</h1>

<?php

if(empty($_POST['subjectLine']) || empty($_POST['email'])){
?>

  <form method="post" action="">
    <input type="text" name="subjectLine" placeholder="Enter your subject line"><br>
    <input type="email" name="email" placeholder="Enter your email address"><br>
    <button type="submit">Test my subject line</button>
  </form>

<?php
}
else{
  $subjectLine = $_POST['subjectLine'];
  $email = $_POST['email'];
  
  //add email address to HubSpot
  $array = array(
  	'properties' => array(
  		array(
  			'property' => 'email',
  			'value' => $email
  		)
  	)
  );

  $json = json_encode($array);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "https://api.hubapi.com/contacts/v1/contact?hapikey=$hubspotApiKey");
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
  curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);
  curl_close($ch);
  
  

  //check if subject line is 50 characters or less
  if(strlen($subjectLine) > 50){
    echo "
      <h2>Make it shorter</h2>
      <p>Your subject line is longer than 50 characters. Try removing a few words to make it 50 characters or less.</p>
    ";
  }
  
  //check if the subject contains any words to avoid
  foreach($wordsToAvoid as $word){
    if(strpos($subjectLine,$word) !== false){
      echo "
        <h2>Remove the word \"$word\"</h2>
        <p>Try removing the word \"$word\" from your subject line to see if it improves your open rate.</p>
      ";
    }
  }
  
  //check if the subject contains any phrases to avoid
  foreach($phrasesToAvoid as $phrase){
    if(strpos($subjectLine,$phrase) !== false){
      echo "
        <h2>Remove the phrase \"$phrase\"</h2>
        <p>Try removing the phrase \"$phrase\" from your subject line to see if it improves your open rate.</p>
      ";
    }
  }
  
  //check if the subject contains any characters to avoid
  foreach($charactersToAvoid as $character){
    if(strpos($subjectLine,$character) !== false){
      echo "
        <h2>Remove the \"$character\" character</h2>
        <p>Try removing the \"$character\" character from your subject line to see if it improves your open rate.</p>
      ";
    }
  }
  
  //check for ALL CAPS words or phrases
  $subjectLineWithNoPunctuationOrNumbers = preg_replace("/[^A-Za-z ]/", "", $subjectLine);
  $wordsThatMakeUpSubjectLine = explode(" ", $subjectLineWithNoPunctuationOrNumbers);
  foreach ($wordsThatMakeUpSubjectLine as $word) {
      if (ctype_upper($word)) {
        echo "
          <h2>Use normal capitalization</h2>
          <p>Try to avoid using words and phrases in ALL CAPS to see if it improves your open rate.</p>
        ";
      }
  }
  
  //check for "you" or "your"
  if(strpos($subjectLine,"you") === false && strpos($subjectLine,"your") === false){
    echo "
      <h2>Consider using \"you\" or \"your\"</h2>
      <p>Try adding \"you\" or \"your\" to see if it improves your open rate.</p>
    ";
  }
  
  //check for a number at the beginning
  $firstWordOfSubjectLine = strtok($subjectLine, " ");
  if(!is_numeric($firstWordOfSubjectLine)){
    echo "
      <h2>Consider starting with a number</h2>
      <p>You could also try starting your subject line with a number. For example: \"10 tips that will help you...\"</p>
    ";
  }

  //check for a question mark
  if(strpos($subjectLine,"?") === false){
    echo "
      <h2>Consider asking a question</h2>
      <p>You could also try asking a question and ending your subject line with a question mark (?) to see if it improves your open rate.</p>
    ";
  }
  
  //give a link to try another subject line
  echo "<p><strong><a href='" . $_SERVER['REQUEST_URI'] . "'>Test another subject line</a></strong></p>";
  
}
?>

</body>

</html>
