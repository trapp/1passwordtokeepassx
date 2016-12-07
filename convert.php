<?php

date_default_timezone_set('Etc/UTC');

$pws = array();
if (($handle = fopen("export.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        foreach($data as &$d) {
          $d = str_replace('\"', '"', $d);
        }
        $pws[] = $data;
    }
    fclose($handle);
}

$doc = new DOMDocument('1.0');
$doc->formatOutput = true;

$file = $doc->createElement('KeePassFile');
$file = $doc->appendChild($file);

$meta = $file->appendChild($doc->createElement('Meta'));
$meta->appendChild($doc->createElement('DatabaseName'))->appendChild($doc->createTextNode('1Password import'));
$meta->appendChild($doc->createElement('DatabaseDescription'))->appendChild($doc->createTextNode('1Password import'));
$meta->appendChild($doc->createElement('DatabaseDescription'))->appendChild($doc->createTextNode('100'));
$meta->appendChild($doc->createElement('HistoryMaxSize'))->appendChild($doc->createTextNode('6291456'));

$root = $doc->createElement('Root');
$root = $file->appendChild($root);

$group = $root->appendChild($doc->createElement('Group'));
$group->appendChild($doc->createElement('UUID'))->appendChild($doc->createTextNode(uuid()));
$group->appendChild($doc->createElement('Name'))->appendChild($doc->createTextNode('Root'));

$loginGroup = $group->appendChild($doc->createElement('Group'));
$loginGroup->appendChild($doc->createElement('UUID'))->appendChild($doc->createTextNode(uuid()));
$loginGroup->appendChild($doc->createElement('Name'))->appendChild($doc->createTextNode('Login'));

$noteGroup = $group->appendChild($doc->createElement('Group'));
$noteGroup->appendChild($doc->createElement('UUID'))->appendChild($doc->createTextNode(uuid()));
$noteGroup->appendChild($doc->createElement('Name'))->appendChild($doc->createTextNode('Secure Note'));

$etcGroup = $group->appendChild($doc->createElement('Group'));
$etcGroup->appendChild($doc->createElement('UUID'))->appendChild($doc->createTextNode(uuid()));
$etcGroup->appendChild($doc->createElement('Name'))->appendChild($doc->createTextNode('1Password import'));

foreach($pws as $pw) {
  // 0 note
  // 1 pw
  // 2 title
  // 3 type
  // 4 url
  // 5 username
  // 6 created
  // 7 modified

  $pwEntry = $doc->createElement('Entry');

  $pwEntry->appendChild($doc->createElement('UUID'))->appendChild($doc->createTextNode(uuid()));

  $times = $pwEntry->appendChild($doc->createElement('Times'));
  $creationTime = $times->appendChild($doc->createElement('CreationTime'));
  $creationTime->appendChild($doc->createTextNode(date('Y-m-d\TH:i:s\Z', $pw[6])));

  $pwEntry->appendChild($doc->createElement('Tags'))->appendChild($doc->createTextNode($pw[3]));

  if (is_numeric($pw[7])) {
    $modTime = $times->appendChild($doc->createElement('LastModificationTime'));
    $modTime->appendChild($doc->createTextNode(date('Y-m-d\TH:i:s\Z', $pw[7])));
  }

  $note = $pwEntry->appendChild($doc->createElement('String'));
  $note->appendChild($doc->createElement('Key'))->appendChild($doc->createTextNode('Notes'));
  $note->appendChild($doc->createElement('Value'))->appendChild($doc->createTextNode($pw[0]));

  $password = $pwEntry->appendChild($doc->createElement('String'));
  $password->appendChild($doc->createElement('Key'))->appendChild($doc->createTextNode('Password'));
  $password->appendChild($doc->createElement('Value'))->appendChild($doc->createTextNode($pw[1]));

  $title = $pwEntry->appendChild($doc->createElement('String'));
  $title->appendChild($doc->createElement('Key'))->appendChild($doc->createTextNode('Title'));
  $title->appendChild($doc->createElement('Value'))->appendChild($doc->createTextNode($pw[2]));

  $url = $pwEntry->appendChild($doc->createElement('String'));
  $url->appendChild($doc->createElement('Key'))->appendChild($doc->createTextNode('URL'));
  $url->appendChild($doc->createElement('Value'))->appendChild($doc->createTextNode($pw[4]));

  $username = $pwEntry->appendChild($doc->createElement('String'));
  $username->appendChild($doc->createElement('Key'))->appendChild($doc->createTextNode('UserName'));
  $username->appendChild($doc->createElement('Value'))->appendChild($doc->createTextNode($pw[5]));

  if ($pw[3] == 'Login') {
    $loginGroup->appendChild($pwEntry);
  } elseif ($pw[3] == 'Secure Note') {
    $noteGroup->appendChild($pwEntry);
  } else {
    $etcGroup->appendChild($pwEntry);
  }
}

echo $doc->saveXml() . "\n";


function uuid() {
  return base64_encode(openssl_random_pseudo_bytes(16));
}
