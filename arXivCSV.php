<?php
// Replace \n with space, remove multiple spaces and trim them from start and end of string
function refactor($val) {
	return trim(preg_replace('/\s\s+/', ' ', str_replace(PHP_EOL, " ", $val)));
}

// Some of the titles and abstracts have a slash or backslash at the end (sic!), so let's remove it
function removeSlash($string) {
	return rtrim($string, ' /\\');
}

// Check if string starts with uppercase character or hypgen
function startsWithUpperOrHyphen($str) {
    $chr = mb_substr ($str, 0, 1, "UTF-8");
    return mb_strtolower($chr, "UTF-8") != $chr || mb_substr ($str, 0, 1, "UTF-8") === '-';
}

// Capitalizes first character of a string
function capitalizeFirstLetter($str) {
	$chr = mb_substr ($str, 0, 1, "UTF-8");
	return mb_strtoupper($chr).mb_substr($str, 1);
}
// Removes categories that don't exist on arxiv, also splits them into subsets
function divideIntoSubs($subjects, $arxiv_categories, $arxiv_subcategories) {
	$categories = [];
	$subcategories = [];
	    foreach ($subjects as $element) {
	    	$elements = explode(" - ", $element);
	    	if (in_array($elements[0], $arxiv_categories)) {
			    if (isset($elements[1])) {
			    	if(in_array($elements[1], $arxiv_subcategories))
			    		$categories[] = $elements[1];
			    	else {
			    		//echo '### Error, incorrect subcategory: ' . $elements[0] . ' => ' . $elements[1] . PHP_EOL;
			    		$subcategories[] = '';
			    	}
			    }
			    else {
			    	$categories[] = $elements[0];
			    }
			}
			//else 
				// echo 'Incorrect category: ' . $elements[0] . PHP_EOL;
		}
	return $categories;
}
function containsBanned($str)
{
	$arr = ["collaboration", "consortium", "group", "information", "project", ";", "_", "committee", "author", "]","\""];
    foreach($arr as $a) {
        if (stripos(mb_strtolower($str),$a) !== false) return true;
    }
    return false;
}
function namesValidator($creators) {
	$names = [];
	$valid = false;
	$individual  = false;
	foreach ($creators as $creator) {
		$creatorName = explode(", ", $creator);
		//Continue if creator has a first name and a last name, and does not contain a banned word
		if (sizeof($creatorName) == 2 && !containsBanned($creator)) {
			$first_name = explode(" ", $creatorName[1]);
			$last_name = explode(" ", $creatorName[0]);
			if (sizeof($first_name) <= 3 && sizeof($last_name) <= 3) {
				foreach ($first_name as &$fn) {
					if (!startsWithUpperOrHyphen($fn) || mb_strlen($fn, "UTF-8") < 2) {
						return array([], false);
					}
				}
				//Capitalizing all the 'van der', 'de', 'von'
				foreach ($last_name as &$ln) {
					if (mb_strlen($ln, "UTF-8") < 2) {
						return array([], false);
					}
					$ln = capitalizeFirstLetter($ln);
				}
				$names[] = implode(" ", $last_name) . ',' . implode(" ", $first_name);
			} else {
				return array([], false);
			}
		} else {
			//print $creator . PHP_EOL;
			return array([], false);
		}

	}
	
	if (!empty($names))
		$valid = true;
	
	return array($names, $valid);
}

$arxiv_categories = file('categories.txt', FILE_IGNORE_NEW_LINES);
$arxiv_subcategories = file('subcategories.txt', FILE_IGNORE_NEW_LINES);

$directory = "xml/";
$filecount = 0;
$files = glob($directory . "*.{xml}", GLOB_BRACE);
if ($files){
	$filecount = count($files);
	print "W katalogu {$directory} znajduje się {$filecount} plików." . PHP_EOL;
	print "Program rozpoczyna swoją pracę." . PHP_EOL;
	$csv = fopen("output.csv", "w");
	$autcsv = fopen("authors.csv", "w");
	$headers = array("id", "title", "url", "abstract", "date", "categories");
	$autheaders = array("id", "first_name", "last_name");
	fputcsv($csv, $headers, ',', '"');
	fputcsv($autcsv, $autheaders, ',', '"');
	$id = 0;
	foreach($files as $file) {
		//print "Przetwarzany plik: {$file}" . PHP_EOL;
	  	$xml = simplexml_load_file(rawurlencode($file));
	  	$ns = $xml->getNamespaces(true);
	  	$publication = $xml->children($ns['dc']);
	  	$namesValidator = namesValidator((array)$publication->creator);
	  	if(!$namesValidator[1])
	  		continue;
		$title = removeSlash(refactor($publication->title));
		$abstract = removeSlash(refactor($publication->description[0]));
		$date = refactor($publication->date[0]);
		$url = refactor($publication->identifier);
		$categories = implode(";", divideIntoSubs((array)$publication->subject, $arxiv_categories, $arxiv_subcategories));
		$authors = /*implode(";", $namesValidator[0])*/ $namesValidator[0];
		foreach ($authors as $author) {
			$names = explode(",", $author);
			fputcsv($autcsv, array($id, removeSlash($names[1]), removeSlash($names[0])), ',', '"');
		}
		fputcsv($csv, array($id, $title, $url, $abstract, $date, $categories), ',', '"');
		$id++;
	} 
	fclose($csv);
}
print "Koniec działania programu." . PHP_EOL;