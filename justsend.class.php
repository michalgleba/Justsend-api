<?php
 
class justsend {
	
	var $_apikey = "";
	var $_apiurl = "https://justsend.pl/api/rest/";
	
	public function __construct($apikey)
	{
		$this->_apikey = $apikey;
	}
	
	/*
		Get translate response code
	*/
	public function getTranslatedResponse($id)
	{
		$lang = array(
			0 	=> "OK",
			100 => "Błąd wewnętrzny aplikacji Justsend",
			101 => "Błąd autoryzacji użytkownika API",
			102 => "Brak uprawnień",
			103 => "Brak wyników dla zadanych parametrów wywołania metody API",
			104 => "Niepoprawny status użytkownika",
			105 => "Nie znaleziono podużytkownika",
			106 => "Użytkownik/podużytkownik nie posiada wystarczającej ilości punktów",
			107 => "Brak uprawnień do wysyłki ze zbieraniem odpowiedzi (typ PREFIX)",
			108 => "Brak uprawnień do wysyłki ze zbieraniem odpowiedzi (typ 9DIGIT)",
			301 => "Brak wystarczających środków na koncie do realizacji wysyłki",
			311 => "Nazwa nadawcy zawiera numer telefonu",
			312 => "Niepoprawna długość nazwy nadawcy"
		);
		return $lang[$id];
	}
	
	/*
		Replace polish chars from SMS
	*/
	public function charset_utf_fix($string) 
	{
		$table = Array(

		//WIN
		"\xb9" => "a", "\xa5" => "A", "\xe6" => "c", "\xc6" => "C",
		"\xea" => "e", "\xca" => "E", "\xb3" => "l", "\xa3" => "L",
		"\xf3" => "o", "\xd3" => "O", "\x9c" => "s", "\x8c" => "S",
		"\x9f" => "z", "\xaf" => "Z", "\xbf" => "z", "\xac" => "Z",
		"\xf1" => "n", "\xd1" => "N",
		
		//UTF
		"\xc4\x85" => "a", "\xc4\x84" => "A", "\xc4\x87" => "c", "\xc4\x86" => "C",
		"\xc4\x99" => "e", "\xc4\x98" => "E", "\xc5\x82" => "l", "\xc5\x81" => "L",
		"\xc3\xb3" => "o", "\xc3\x93" => "O", "\xc5\x9b" => "s", "\xc5\x9a" => "S",
		"\xc5\xbc" => "z", "\xc5\xbb" => "Z", "\xc5\xba" => "z", "\xc5\xb9" => "Z",
		"\xc5\x84" => "n", "\xc5\x83" => "N",
		
		//ISO
		"\xb1" => "a", "\xa1" => "A", "\xe6" => "c", "\xc6" => "C",
		"\xea" => "e", "\xca" => "E", "\xb3" => "l", "\xa3" => "L",
		"\xf3" => "o", "\xd3" => "O", "\xb6" => "s", "\xa6" => "S",
		"\xbc" => "z", "\xac" => "Z", "\xbf" => "z", "\xaf" => "Z",
		"\xf1" => "n", "\xd1" => "N");

		return strtr($string,$table);
	}
		
	public function sendSMS($numbers, $message, $premium = false, $premium_name = "")
	{
		$message = $this->charset_utf_fix($message);
		if (!is_array($numbers))
			$numbers = array($numbers);
		$date = new DateTime();
		$url = $this->_apiurl."bulk/send/".$this->_apikey;

		$parameters = array(
			"name" => "API",
			"to" => $numbers,
			"message" => $message,
			"bulkVariant" => ($premium) ? "PRO" : "ECO",
			"sendDate" => $date->format("c"),
			"from" => $premium_name,
			"groupId" => 0 
		);
		$parameters = json_encode($parameters);
		
		$hook = curl_init( $url );
		curl_setopt($hook, CURLOPT_URL, $url);
		curl_setopt($hook, CURLOPT_POST, TRUE);
		curl_setopt($hook, CURLOPT_POSTFIELDS, $parameters);
		curl_setopt($hook, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($hook, CURLOPT_HTTPHEADER, array(                                                                          
					'Content-Type: application/json',                                                                                
					'Content-Length: ' . strlen($parameters))                                                                       
					);   
		$transfer = curl_exec($hook);
		return $transfer;
	}
	
}

?>