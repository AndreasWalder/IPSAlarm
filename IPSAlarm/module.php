<?
    class IPSAlarm extends IPSModule {
		
		//Wird beim Erstellen vom Modul aufgerufen
        public function Create()
        {
            //Never delete this line!
            parent::Create();
			
			
			
			$this->RegisterPropertyInteger("Duration", 1);
			$this->RegisterPropertyString("ListWochentage", "");
            $this->RegisterPropertyInteger("OutputID", 0);
			
			$timestamp = time(); 
            $datum = date("d.m.Y",$timestamp); 
            $uhrzeit = date("H:i:s",$timestamp);  
			$this->RegisterPropertyString("PropertyTimeFrom", $uhrzeit);
			$this->RegisterPropertyString("PropertyTimeTo", $uhrzeit);
			
			$this->RegisterPropertyString("PushNachricht", "");
			$this->RegisterPropertyBoolean ("PushAktiv", false);
			$this->RegisterPropertyInteger("PushInstanceID", 0);
			
			$this->RegisterPropertyString("AlexaNachricht", "");
			$this->RegisterPropertyBoolean ("AlexaAktiv", false);
			$this->RegisterPropertyString("ListAlexaDevices", "");
		
			
			// Erstellt einen Timer mit dem Namen und einem Intervall und ein Ziel. 
            $this->RegisterTimer("OffTimer", 0, "TIMER_Stop(\$_IPS['TARGET']);");
			$this->RegisterTimer("Update", 0, "TIMER_Update(\$_IPS['TARGET']);");
			$this->RegisterTimer("CheckEvent", 0, "TIMER_CheckEvent(\$_IPS['TARGET']);");
			
			//Erstellen eines Variablenprofile für Typ Boolean
			//$associations = [];
			//$associations[] = ['Wert' => 1, 'Name' => 'An'];
			//$associations[] = ['Wert' => 0, 'Name' => 'Aus'];
			//$this->CreateVarProfile('IPSAlarm.STATUS', 0, '', 0, 0, 1, 1, 'Information', $associations);			
			//$this->RegisterVariableBoolean("Status", "Status", "IPSAlarm.STATUS", 5);
			
			$this->RegisterVariableBoolean("Test", "Test", "~Switch", 0);
			
			
			//$associations = [];
			//$associations[] = ['Wert' => 1, 'Name' => 'Taster', 'Farbe' => 0xFFD700];
			//$associations[] = ['Wert' => 0, 'Name' => ' ', 'Farbe' => -1];
			//$this->CreateVarProfile('IPSALARM.TASTEN', 0, '', 0, 0, 1, 1, 'Power', $associations);			
			//$this->RegisterVariableBoolean("Taster", "Taster", "IPSAlarm.TASTEN", 1);
			
			//Erstellen eines Variablenprofile für Typ Boolean
			//$associations = [];
			//$associations[] = ['Wert' => true, 'Name' => 'An', 'Farbe' => 0x00BFFF]; //Farbe Blau
			//$associations[] = ['Wert' => false, 'Name' => 'Aus', 'Farbe' => -1];
			//$this->CreateVarProfile('IPSAlarm.AKTIV', 0, '', 0, 0, 1, 1, 'Power', $associations);

			
			//$this->RegisterVariableBoolean("Active", "Timer Aktiv", "IPSTimer.AKTIV", 20);	
			
			//$associations = '';
			//$associations[] = ['Wert' => 1, 'Name' => 'Anwesend'];
			//$associations[] = ['Wert' => 0, 'Name' => 'Abwesend'];
			//$this->CreateVarProfile('IPSTimer.ALARM', 1, ' min', 0, $this->ReadPropertyInteger("Duration"), 0, 1, 'Clock', $associations);
			//$this->RegisterVariableInteger("Ablaufzeit", "Ablaufzeit", "IPSAlarm.ALARM", 10);
			
			
			//$associations = '';
			//$associations[] = ['Wert' => 1, 'Name' => 'Anwesend'];
			//$associations[] = ['Wert' => 0, 'Name' => 'Abwesend'];
			//$this->CreateVarProfile('IPSAlarm.DAUER', 1, ' min', 0, 60, 1, 1, 'Clock', $associations);	
			//$this->RegisterVariableInteger("Dauer", "Dauer", "IPSAlarm.DAUER", 30);
	
			
			//$this->EnableAction("Dauer");	
            //$this->EnableAction("Schalten");
			//$this->EnableAction("Taster");
			//$this->EnableAction("Active");
			
        }
		
		
		// Variablenprofile erstellen
		private function CreateVarProfile($Name, $ProfileType, $Suffix, $MinValue, $MaxValue, $StepSize, $Digits, $Icon, $Asscociations = '')
		{
			if (!IPS_VariableProfileExists($Name)) {
				IPS_CreateVariableProfile($Name, $ProfileType);
				IPS_SetVariableProfileText($Name, '', $Suffix);
				IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize);
				IPS_SetVariableProfileDigits($Name, $Digits);
				IPS_SetVariableProfileIcon($Name, $Icon);
				if ($Asscociations != '') {
					foreach ($Asscociations as $a) {
						$w = isset($a['Wert']) ? $a['Wert'] : '';
						$n = isset($a['Name']) ? $a['Name'] : '';
						$i = isset($a['Icon']) ? $a['Icon'] : '';
						$f = isset($a['Farbe']) ? $a['Farbe'] : 0;
						IPS_SetVariableProfileAssociation($Name, $w, $n, $i, $f);
					}
				}
			}
			else {
			 IPS_SetVariableProfileText($Name, '', $Suffix);
			 IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize);
			 IPS_SetVariableProfileDigits($Name, $Digits);
			 IPS_SetVariableProfileIcon($Name, $Icon);
			}
		}
		
		// Wird beim Ändern im Modul aufgerufen
        public function ApplyChanges() {
            //Never delete this line!
            parent::ApplyChanges();
			//Erstellen eines Variablenprofile für Typ Integer
			$associations = '';
			//$associations[] = ['Wert' => 1, 'Name' => 'Anwesend'];
			//$associations[] = ['Wert' => 0, 'Name' => 'Abwesend'];
			$this->CreateVarProfile('IPSAlarm.Wiederholung', 1, ' min', 0, $this->ReadPropertyInteger("Duration"), 0, 1, 'Clock', $associations);			
			$this->RegisterVariableInteger("Wiederholung", "Wiederholung", "IPSAlarm.Wiederholung", 10);
			//$triggerID = $this->GetIDForIdent("Status");
            //$this->RegisterMessage($triggerID, 10603 /* VM_UPDATE */);
			
		
			SetValue($this->GetIDForIdent("Wiederholung"), $this->ReadPropertyInteger("Duration"));
			//SetValue($this->GetIDForIdent("Dauer"), $this->ReadPropertyInteger("Duration"));
			
			
			//if ($this->ReadPropertyBoolean("SchalterEnable")) {
			//   IPS_SetHidden($this->GetIDForIdent("Schalten"), true); //Objekt verstecken
			//}
			//else {
			//   IPS_SetHidden($this->GetIDForIdent("Schalten"), false); //Objekt anzeigen
			//}
			
			//if ($this->ReadPropertyBoolean("TasterEnable")) {
			//   IPS_SetHidden($this->GetIDForIdent("Taster"), true); //Objekt verstecken
			//}
			//else {
			//   IPS_SetHidden($this->GetIDForIdent("Taster"), false); //Objekt anzeigen
			//}
			
        }
		
		
		
		public function GetConfigurationForm()
		{
			
			$data = json_decode(file_get_contents(__DIR__ . "/form.json"));
			
			//Only add default element if we do not have anything in persistence
			if($this->ReadPropertyString("ListAlexaDevices") == "") {			
				
			} else {
				//Annotate existing elements
				$ListAlexaDevices = json_decode($this->ReadPropertyString("ListAlexaDevices"));
				foreach($ListAlexaDevices as $treeRow) {
					//We only need to add annotations. Remaining data is merged from persistance automatically.
					//Order is determinted by the order of array elements
					if(IPS_ObjectExists($treeRow->instanceID)) {
						$data->elements[0]->values[] = Array(
							"state" => "OK!"
						);
					} else {
						$data->elements[0]->values[] = Array(
							"state" => "FAIL!",
							"rowColor" => "#ff0000"
						);
					}						
				}			
			}
			
			
			//Only add default element if we do not have anything in persistence
			if($this->ReadPropertyString("ListWochentage") == "") {			
				
			} else {
				//Annotate existing elements
				$ListWochentage = json_decode($this->ReadPropertyString("ListWochentage"));
				foreach($ListWochentage as $treeRow) {
					//We only need to add annotations. Remaining data is merged from persistance automatically.
					//Order is determinted by the order of array elements
					if(IPS_ObjectExists($treeRow->Wochentag)) {
						$data->elements[0]->values[] = Array(
							
						);
					} else {
						$data->elements[0]->values[] = Array(
							"rowColor" => "#ff0000"
						);
					}						
				}			
			}
			
			return json_encode($data);
		
		}	
		
		public function MessageSink ($TimeStamp, $SenderID, $Message, $Data) {
			
			if (!GetValue($this->GetIDForIdent("Status"))) {
			   SetValue($this->GetIDForIdent("Ablaufzeit"), 0);
			}
			
			$triggerID = $this->GetIDForIdent("Status");
            if (($SenderID == $triggerID) && ($Message == 10603) && (boolval($Data[0]))) {
                $this->Start();
            }
        }
		
		// Ausgelöste Action beim betätigen von Elementen in der Webfront
        public function RequestAction($Ident, $Value) {
            switch($Ident) {
                case "Active":
				
                    $this->SetActive($Value);
					
					$this->SetTimerInterval("CheckEvent", 300);
							
                    /*							
					$EreignisID = @IPS_GetEventIDByName("IPSTimerEventAn", $this->GetIDForIdent("Status"));
                    if ($EreignisID === false)
					{
					$eidan = IPS_CreateEvent(0);                									  	//Ausgelöstes Ereignis 		
					IPS_SetEventTrigger($eidan, 4, $this->ReadPropertyInteger("OutputID"));         	//Bei Änderung von Variable mit ID 15754
					IPS_SetEventTriggerValue($eidan, true);		                                  		//Nur auf TRUE Werte auslösen
					// Füge eine Regel mit der ID 2 hinzu: Variable "Status" == true
					IPS_SetEventCondition($eidan, 0, 0, 0);
                    IPS_SetEventConditionVariableRule($eidan, 0, 1, $this->GetIDForIdent("Status"), 0, false);
					//IPS_SetEventConditionVariableRule($eidan, 0, 2, $this->GetIDForIdent("Active"), 0, true);
					IPS_SetParent($eidan, $this->GetIDForIdent("Status"));                  			//Ereigniss zuordnen zu Variable "Status"  
					IPS_SetEventTriggerValue($eidan, true);		                                	    //Nur auf TRUE Werte auslösen
					IPS_SetIdent($eidan, "IPSTimerEventAn");											//Ident setzen internen Namen
					IPS_SetName($eidan, "IPSTimerEventAn");								                //Name dem Event zuordnen
					IPS_SetEventActive($eidan, true);          								     	    //Ereignis aktivieren
					}
					
					$EreignisID = @IPS_GetEventIDByName("IPSTimerEventOFF", $this->GetIDForIdent("Status"));
					
                    if ($EreignisID === false)
					{
					$eidaus = IPS_CreateEvent(0);                									  //Ausgelöstes Ereignis	
					IPS_SetEventTrigger($eidaus, 4, $this->ReadPropertyInteger("OutputID"));         //Bei Änderung von Variable mit ID 15754
					IPS_SetEventTriggerValue($eidaus, false);		                                  //Nur auf false Werte auslösen
					// Füge eine Regel mit der ID 2 hinzu: Variable "Status" == true
					IPS_SetEventCondition($eidaus, 0, 0, 0);
                    IPS_SetEventConditionVariableRule($eidaus, 0, 1, $this->GetIDForIdent("Status"), 0, true);
					//IPS_SetEventConditionVariableRule($eidaus, 0, 2, $this->GetIDForIdent("Active"), 0, true);
					IPS_SetParent($eidaus, $this->GetIDForIdent("Status"));                  //Ereigniss zuordnen zu Variable "Status"
					IPS_SetEventTriggerValue($eidaus, false);		                                  //Nur auf false Werte auslösen					
					IPS_SetIdent($eidaus, "IPSTimerEventOFF");
					IPS_SetName($eidaus, "IPSTimerEventOFF");								              //Name dem Event zuordnen
					IPS_SetEventActive($eidaus, true);          								      //Ereignis aktivieren
					}
					
					$EreignisID = @IPS_GetEventIDByName("IPSTimerSchaltenAn", $this->GetIDForIdent("Schalten"));
					
                    if ($EreignisID === false)
					{
					$eidan = IPS_CreateEvent(0);                									  //Ausgelöstes Ereignis 		
					IPS_SetEventTrigger($eidan, 4, $this->ReadPropertyInteger("OutputID"));         //Bei Änderung von Variable mit ID 15754
					IPS_SetEventTriggerValue($eidan, true);		                                  //Nur auf TRUE Werte auslösen
					// Füge eine Regel mit der ID 2 hinzu: Variable "Schalten" == true
					IPS_SetEventCondition($eidan, 0, 0, 0);
                    IPS_SetEventConditionVariableRule($eidan, 0, 1, $this->GetIDForIdent("Schalten"), 0, false);
					//IPS_SetEventConditionVariableRule($eidan, 0, 2, $this->GetIDForIdent("Active"), 0, true);
					IPS_SetParent($eidan, $this->GetIDForIdent("Schalten"));                  //Ereigniss zuordnen zu Variable "Schalten" 
					IPS_SetEventTriggerValue($eidan, true);		                                  //Nur auf TRUE Werte auslösen					
					IPS_SetIdent($eidan, "IPSTimerSchaltenAn");
					IPS_SetName($eidan, "IPSTimerSchaltenAn");								              //Name dem Event zuordnen
					IPS_SetEventActive($eidan, true);          								      //Ereignis aktivieren
					}
					
					$EreignisID = @IPS_GetEventIDByName("IPSTimerSchaltenAus", $this->GetIDForIdent("Schalten"));
                    if ($EreignisID === false)
					{
					$eidaus = IPS_CreateEvent(0);                										  //Ausgelöstes Ereignis	
					IPS_SetEventTrigger($eidaus, 4, $this->ReadPropertyInteger("OutputID"));       		  //Bei Änderung von Variable mit ID 15754
					IPS_SetEventTriggerValue($eidaus, false);		                             	     //Nur auf false Werte auslösen
					// Füge eine Regel mit der ID 2 hinzu: Variable "Schalten" == true
					IPS_SetEventCondition($eidaus, 0, 0, 0);
                    IPS_SetEventConditionVariableRule($eidaus, 0, 1, $this->GetIDForIdent("Schalten"), 0, true);
					//IPS_SetEventConditionVariableRule($eidaus, 0, 2, $this->GetIDForIdent("Active"), 0, true);
					IPS_SetParent($eidaus, $this->GetIDForIdent("Schalten"));               			   //Ereigniss zuordnen zu Variable "Schalten" 
					IPS_SetEventTriggerValue($eidaus, false);		                         	         //Nur auf false Werte auslösen					
					IPS_SetIdent($eidaus, "IPSTimerSchaltenAus");
					IPS_SetName($eidaus, "IPSTimerSchaltenAus");								         //Name dem Event zuordnen
					IPS_SetEventActive($eidaus, true);          								   	    //Ereignis aktivieren
					}
					*/		
                    break;
					
				case "Schalten":
				
				    $this->SetTimerInterval("CheckEvent", 300);
				    	
				    if (!GetValue($this->GetIDForIdent("Active"))){			
			            SetValue($this->GetIDForIdent("Ablaufzeit"), 0);
                       }
					
				    $this->SwitchVariable($Value);
				    SetValue($this->GetIDForIdent("Schalten"), $Value);
					break;
					
				case "Taster":
				
				    $this->SetTimerInterval("CheckEvent", 300);
				    	
				    if (!GetValue($this->GetIDForIdent("Active"))){			
			            SetValue($this->GetIDForIdent("Ablaufzeit"), 0);
                       }
					
					if (GetValue($this->GetIDForIdent("Taster")) == true){			
			            
						SetValue($this->GetIDForIdent("Taster"), false);
						$this->SwitchVariable(false);
                       }
					else {
						   
					    SetValue($this->GetIDForIdent("Taster"), true); 
                        $this->SwitchVariable(true);						
					   }
					
				    
					
				    
					break;
					
				case "Dauer":
				 
					$this->SetValueDauer($Value);				
					break;
					
                default:
                    throw new Exception("Invalid ident");
            }
        }
        
        public function SetActive(bool $Value) {
			if ($Value == false) {
		      $this->SwitchVariable(false);
			}		
            SetValue($this->GetIDForIdent("Active"), $Value);
        }
		
		
		public function SetValueDauer(int $Value) {		
            SetValue($this->GetIDForIdent("Dauer"), $Value);
        }
		
        
        public function Start(){
            if (!GetValue($this->GetIDForIdent("Active"))){
                return;
            }
            $duration = $this->ReadPropertyInteger("Duration");
			
			If ($duration <> GetValue($this->GetIDForIdent("Dauer"))) {
			$duration = GetValue($this->GetIDForIdent("Dauer"));
			}
			
	        $this->SwitchVariable(true);
            $this->SetTimerInterval("OffTimer", $duration * 60 * 1000);
			$this->SetTimerInterval("Update", 60 * 1000);
			SetValue($this->GetIDForIdent("Ablaufzeit"), $duration);
			SetValue($this->GetIDForIdent("Schalten"), true);
        }
		
		
        public function Stop(){
			SetValue($this->GetIDForIdent("Status"), false);
            $this->SwitchVariable(false);
            $this->SetTimerInterval("OffTimer", 0);
			SetValue($this->GetIDForIdent("Ablaufzeit"), 0);
			SetValue($this->GetIDForIdent("Schalten"), false);
        }
		
		
		public function Update(){
			if (GetValue($this->GetIDForIdent("Ablaufzeit")) == 0) {
			   $this->SetTimerInterval("Update", 0);
			   return;
			}
			$UpdateTimer = GetValue($this->GetIDForIdent("Ablaufzeit"));
			$UpdateTimer = $UpdateTimer - 1;
            SetValue($this->GetIDForIdent("Ablaufzeit"), $UpdateTimer);
        }
		
		public function CheckEvent(){
			if (GetValue($this->ReadPropertyInteger("OutputID")) == true) {
			 if (GetValue($this->GetIDForIdent("Schalten")) == false) {
			   SetValue($this->GetIDForIdent("Schalten"), true);	
               SetValue($this->GetIDForIdent("Status"), true);
			   SetValue($this->GetIDForIdent("Taster"), true); 			   
			 }
			}
			
			if (GetValue($this->ReadPropertyInteger("OutputID")) == false) {
			 if (GetValue($this->GetIDForIdent("Schalten")) == true) {
			   SetValue($this->GetIDForIdent("Schalten"), false);	
               SetValue($this->GetIDForIdent("Status"), false);
			   SetValue($this->GetIDForIdent("Taster"), false); 			   
			 }
			}
			
			if (GetValue($this->GetIDForIdent("Schalten")) == false) {
			 if (GetValue($this->GetIDForIdent("Status")) == true) {
			   $this->SwitchVariable(false);
               SetValue($this->GetIDForIdent("Status"), false);
			   SetValue($this->GetIDForIdent("Taster"), false); 			   
			 }
			}
			
			if (GetValue($this->GetIDForIdent("Schalten")) == true) {
			 if (GetValue($this->GetIDForIdent("Status")) == false) {
			   $this->SwitchVariable(true);
               SetValue($this->GetIDForIdent("Status"), true);
			   SetValue($this->GetIDForIdent("Taster"), true); 			   
			 }
			}
			
			/*
			if (GetValue($this->GetIDForIdent("Schalten")) == false) {
			  SetValue($this->GetIDForIdent("Status"), false);
			  SetValue($this->GetIDForIdent("Taster"), false); 
			}
			else{
			  SetValue($this->GetIDForIdent("Status"), true);
			  SetValue($this->GetIDForIdent("Taster"), true); 
			}
			*/
		}
		
		
        private function SwitchVariable(bool $Value){
            $outputID = $this->ReadPropertyInteger("OutputID");
            $object = IPS_GetObject($outputID);
            $variable = IPS_GetVariable($outputID);
            $actionID = $this->GetProfileAction($variable);
			SetValue($this->GetIDForIdent("Taster"), $Value); 	
            //Quit if actionID is not a valid target
            if($actionID < 10000){
                echo $this->Translate("Die Ausgabevariable hat keine Variablenaktion! (Aktion hinzufügen)");
                return;
            }
            $profileName = $this->GetProfileName($variable);
            //If we somehow do not have a profile take care that we do not fail immediately
            if($profileName != "") {
                //If we are enabling analog devices we want to switch to the maximum value (e.g. 100%)
                if ($Value) {
                    $actionValue = IPS_GetVariableProfile($profileName)['MaxValue'];
                } else {
                    $actionValue = 0;
                }
                //Reduce to boolean if required
                if($variable['VariableType'] == 0) {
                    $actionValue = ($actionValue > 0);
                }
            } else {
                $actionValue = $Value;
            }
            if(IPS_InstanceExists($actionID)){
                IPS_RequestAction($actionID, $object['ObjectIdent'], $actionValue);
            } else if(IPS_ScriptExists($actionID)) {
                echo IPS_RunScriptWaitEx($actionID, Array("VARIABLE" => $outputID, "VALUE" => $actionValue));
            }
        }
		
		
        private function GetProfileName($variable){
            if($variable['VariableCustomProfile'] != ""){
                return $variable['VariableCustomProfile'];
            } else {
                return $variable['VariableProfile'];
            }
        }
		
		
        private function GetProfileAction($variable){
            if($variable['VariableCustomAction'] > 0){
                return $variable['VariableCustomAction'];
            } else {
                return $variable['VariableAction'];
            }
        }
    }
?>