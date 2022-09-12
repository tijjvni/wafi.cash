<?php

	namespace App;


	class User {

		public $currencies;
		public $balance;

		public function __construct(){
			$this->currencies = $this->getCurrencies();
			
			foreach($this->currencies as $key => $currency){
				$this->setBalance($key,0);
			}		
		}

		public function getCurrencies(){
			return 	$currencies = array(
				"GBP" => "0.86",
				"USD" => "1",
				"YUAN" => "6.89",
				"NGN" => "415"
			);
		}


		public function getWallet(){
			$wallet = array();
			$currencies = $this->currencies;
			foreach($currencies as $curr => $rate){
				$wallet[$curr] = $this->getBalance($curr);
			}

			return $wallet;
		}

		public function getBalance($currency){
			return $this->balance[$currency];
		}

		public function setBalance($currency,$amount){
			return $this->balance[$currency] = $amount;
		}


		public function transfer($currency,$amount,$user){

			if($amount > $this->getBalance($currency) ){
				// return $this->checkOtherCurrenciesBalance($currency,$amount);
				if(!$this->checkOtherCurrenciesBalance($currency,$amount)){
					return "Insufficient Balance";
				}
			}

			$this->setBalance($currency,$this->getBalance($currency) - $amount);
			$user->setbalance($currency,$user->getBalance($currency) + $amount);

			return $this->getWallet();
		}

		private function checkOtherCurrenciesBalance($currency,$amount){

			$amountToFind = $amount - $this->getBalance($currency);
			$currencies = $this->currencies;

			$whatWeGot = 0;


			foreach($currencies as $curr => $rate){
				if($curr != $currency && $whatWeGot < $amountToFind){
					$currbalance = $this->getBalance($curr);
					$lookingFor = $rate * ($amountToFind - $whatWeGot);

					if($currbalance >= $lookingFor){
						$this->setBalance($curr,$currbalance - $lookingFor);
						$this->setBalance($currency,$this->getBalance($currency) + $this->convertCurrency($curr,$currency,$lookingFor));

						$whatWeGot += $this->convertCurrency($curr,$currency,$lookingFor);

						// return $this->getBalance($currency);

						
					}else {
						$this->setBalance($curr,0);
						$this->setBalance($currency, $this->getBalance($currency) + $this->convertCurrency($curr,$currency,$currbalance) );

						$whatWeGot += $this->convertCurrency($curr,$currency,$currbalance);						
					}


				}
			}


			if($whatWeGot < $amountToFind){
				return false;
			}

			return true;

		}


		public function convertCurrency($from,$to,$amount){
			$fromRate = $this->currencies[$from];
			$toRate = $this->currencies[$to];

			return ($amount/$fromRate) * $toRate;
		}

	}


