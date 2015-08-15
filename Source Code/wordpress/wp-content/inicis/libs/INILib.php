<?php
/**
 * Copyright (C) 2007 INICIS Inc.
 *
 * �ش� ���̺귯���� ���� �����Ǿ�� �ȵ˴ϴ�.
 * ���Ƿ� ������ �ڵ忡 ���� å���� �������� �����ڿ��� ������ �˷��帳�ϴ�.
 *
 */


	require_once('INICls.php');
	require_once('INISoc.php');

class INIpay50
{
	var $m_type; 				// �ŷ� ����
	var $m_resulterrcode;       // �����޼��� �����ڵ�
	var $m_connIP; 
	var $m_cancelRC = 0; 

	var $m_Data;
	var $m_Log;
	var $m_Socket;
	var $m_Crypto;

	var $m_REQUEST 	= array();
	var $m_REQUEST2 = array();
	var $m_RESULT 	= array();

	function INIpay()
	{
		$this->UnsetField();
	}

	function UnsetField()
	{
		unset($this->m_REQUEST); 
		unset($this->m_RESULT); 
	}

	/*--------------------------------------------------*/
	/*																									*/
	/* ����/���� ��û�� Set or Add                      */
	/*																									*/
 	/*--------------------------------------------------*/
	function SetField( $key, $val ) //Default Entity
	{
		$this->m_REQUEST[$key] = $val;
	}
	function SetXPath( $xpath, $val ) //User Defined Entity
	{
		$this->m_REQUEST2[$xpath] = $val;
	}

	/*--------------------------------------------------*/
	/*																									*/
	/* ����/���� ������ fetch                           */
	/*																									*/
 	/*--------------------------------------------------*/
	function GetResult( $name ) //Default Entity
	{
		$result = $this->m_RESULT[$name];
		if( $result == "" )
			$result = $this->m_Data->GetXMLData( $name );
		if( $result == "" )
			$result = $this->m_Data->m_RESULT[$name];
		return $result;
	}

	/*--------------------------------------------------*/
	/*																									*/
	/* ����/���� ó�� ����                              */
	/*																									*/
 	/*--------------------------------------------------*/
	function startAction()
	{
		
		/*--------------------------------------------------*/
		/* Overhead Operation                               */
  	/*--------------------------------------------------*/
		$this->m_Data = new INIData( $this->m_REQUEST, $this->m_REQUEST2 );
		
		/*--------------------------------------------------*/
		/* Log Start																				*/
   	/*--------------------------------------------------*/
		$this->m_Log = new INILog( $this->m_REQUEST );
		if(!$this->m_Log->StartLog()) 
		{
			$this->MakeTXErrMsg( LOG_OPEN_ERR, "�α������� ������ �����ϴ�.[".$this->m_REQUEST["inipayhome"]."]"); 
			return;
		}

		/*--------------------------------------------------*/
		/* Logging Request Parameter												*/
   	/*--------------------------------------------------*/
		$this->m_Log->WriteLog( DEBUG, $this->m_REQUEST );

		/*--------------------------------------------------*/
		/* Set Type																					*/
   	/*--------------------------------------------------*/
		$this->m_type = $this->m_REQUEST["type"];

		/*--------------------------------------------------*/
		/* Check Field																			*/
   	/*--------------------------------------------------*/
		if( !$this->m_Data->CheckField() )
		{
			$err_msg = "�ʼ��׸�(".$this->m_Data->m_ErrMsg.")�� �����Ǿ����ϴ�.";
			$this->MakeTXErrMsg( $this->m_Data->m_ErrCode, $err_msg ); 
			$this->m_Log->WriteLog( ERROR, $err_msg );
			$this->m_Log->CloseLog( $this->GetResult(NM_RESULTMSG) );
			return;
		}
		$this->m_Log->WriteLog( INFO, "Check Field OK" );

		/*--------------------------------------------------*/
		//���������������� Ű����. ���⼭ ��!!
		/*--------------------------------------------------*/
		if( $this->m_type == TYPE_CHKFAKE )
		{
			return $this->MakeChkFake();
		}

		/*--------------------------------------------------*/
		//Generate TID
   	/*--------------------------------------------------*/
  	if( $this->m_type == TYPE_SECUREPAY   || $this->m_type == TYPE_FORMPAY  || $this->m_type == TYPE_OCBSAVE  		|| 
      	$this->m_type == TYPE_AUTHBILL 		|| $this->m_type == TYPE_FORMAUTH || $this->m_type == TYPE_REQREALBILL	|| 
				$this->m_type == TYPE_REPAY   || $this->m_type == TYPE_VACCTREPAY || $this->m_type == TYPE_RECEIPT	|| $this->m_type == TYPE_AUTH      
    )
		{
			if(!$this->m_Data->MakeTID()) 
			{
				$err_msg = "TID������ �����߽��ϴ�.::".$this->m_Data->m_sTID;
				$this->m_Log->WriteLog( ERROR, $err_msg );
				$this->MakeTXErrMsg( MAKE_TID_ERR, $err_msg ); 
				$this->m_Log->CloseLog( $this->GetResult(NM_RESULTMSG) );
				return;
			}
			$this->m_Log->WriteLog( INFO, 'Make TID OK '.$this->m_Data->m_sTID );
		} 

		$this->m_Crypto = new INICrypto( $this->m_REQUEST );

		/*--------------------------------------------------*/
		//PI����Ű �ε�
		/*--------------------------------------------------*/
		$this->m_Data->ParsePIEncrypted();
		$this->m_Log->WriteLog( INFO, "PI PUB KEY LOAD OK [".$this->m_Data->m_PIPGPubSN."]" );

		/*--------------------------------------------------*/
		//PG����Ű �ε�
		/*--------------------------------------------------*/
		if( ($rtv = $this->m_Crypto->LoadPGPubKey( $pg_cert_SN )) != OK)
		{
			$err_msg = "PG����Ű �ε�����";
			$this->m_Log->WriteLog( ERROR, $err_msg );
			$this->MakeTXErrMsg( $rtv, $err_msg ); 
			$this->m_Log->CloseLog( $this->GetResult(NM_RESULTMSG) );
			return;
		}
		$this->m_Data->m_TXPGPubSN = $pg_cert_SN;
		$this->m_Log->WriteLog( INFO, "PG PUB KEY LOAD OK [".$this->m_Data->m_TXPGPubSN."]" );

		/*--------------------------------------------------*/
		//��������Ű �ε�
		/*--------------------------------------------------*/
		if( ($rtv = $this->m_Crypto->LoadMPrivKey()) != OK ) 
		{
			$err_msg = "��������Ű �ε�����";
			$this->m_Log->WriteLog( ERROR, $err_msg );
			$this->MakeTXErrMsg( $rtv, $err_msg ); 
			$this->m_Log->CloseLog( $this->GetResult(NM_RESULTMSG) );
			$this->m_Crypto->FreePubKey();
			return;
		}
		$this->m_Log->WriteLog( INFO, "MERCHANT PRIV KEY LOAD OK" );

		/*--------------------------------------------------*/
		//���� ����Ű �ε�(SN �� �˱�����!!)
		/*--------------------------------------------------*/
		if( ($rtv = $this->m_Crypto->LoadMPubKey( $m_cert_SN )) != OK)
		{
			$err_msg = "��������Ű �ε�����";
			$this->m_Log->WriteLog( ERROR, $err_msg );
			$this->MakeTXErrMsg( $rtv, $err_msg ); 
			$this->m_Log->CloseLog( $this->GetResult(NM_RESULTMSG) );
			return;
		}
		$this->m_Data->m_MPubSN = $m_cert_SN;
		$this->m_Log->WriteLog( INFO, "MERCHANT PUB KEY LOAD OK [".$this->m_Data->m_MPubSN."]" );

		/*--------------------------------------------------*/
		//������ ��ȣȭ( formpay, cancel, repay, recept, inquiry )
		/*--------------------------------------------------*/
		if( $this->m_type == TYPE_CANCEL	|| $this->m_type == TYPE_REPAY		||  $this->m_type == TYPE_VACCTREPAY ||
			  $this->m_type == TYPE_FORMPAY	|| $this->m_type == TYPE_RECEIPT 	|| 
				$this->m_type == TYPE_CAPTURE || $this->m_type == TYPE_INQUIRY || 
				($this->m_type == TYPE_ESCROW && $this->m_Data->m_EscrowType == TYPE_ESCROW_DLV ) ||
				($this->m_type == TYPE_ESCROW && $this->m_Data->m_EscrowType == TYPE_ESCROW_DNY_CNF ) ||
				$this->m_type == TYPE_REFUND
			)
		{
			if( ($rtv = $this->m_Data->MakeEncrypt( $this->m_Crypto )) != OK )
			{
				$err_msg = "��ȣȭ ����";
				$this->m_Log->WriteLog( ERROR, $err_msg );
				$this->MakeTXErrMsg( $rtv, $err_msg ); 
				$this->m_Log->CloseLog( $this->GetResult(NM_RESULTMSG) );
				return;
			}
			//$this->m_Log->WriteLog( DEBUG, "MAKE ENCRYPT OK" );
			$this->m_Log->WriteLog( DEBUG, "MAKE ENCRYPT OK[".$this->m_Data->m_EncBody."]" );
		}

		/*--------------------------------------------------*/
		//��������(Body)
		/*--------------------------------------------------*/
		$this->m_Data->MakeBody();
		$this->m_Log->WriteLog( INFO, "MAKE BODY OK" );
		//$this->m_Log->WriteLog( INFO, "MAKE BODY OK[".$this->m_Data->m_sBody."]" );

		/*--------------------------------------------------*/
		//����(sign)
		/*--------------------------------------------------*/
		if( ($rtv = $this->m_Crypto->Sign( $this->m_Data->m_sBody, $sign )) != OK )
		{
			$err_msg = "���ν���";
			$this->m_Log->WriteLog( ERROR, $err_msg );
			$this->MakeTXErrMsg( $rtv, $err_msg ); 
			$this->m_Log->CloseLog( $this->GetResult(NM_RESULTMSG) );
			$this->m_Crypto->FreeAllKey();
			return;
		}
		$this->m_Data->m_sTail = $sign;
		$this->m_Log->WriteLog( INFO, "SIGN OK" );
		//$this->m_Log->WriteLog( INFO, "SIGN OK[".$sign."]" );

		/*--------------------------------------------------*/
		//��������(Head)
		/*--------------------------------------------------*/
		$this->m_Data->MakeHead();
		$this->m_Log->WriteLog( INFO, "MAKE HEAD OK" );
		//$this->m_Log->WriteLog( INFO, "MAKE HEAD OK[".$head."]" );

		$this->m_Log->WriteLog( INFO, "MSG_TO_PG:[".$this->m_Data->m_sMsg."]" );

		/*--------------------------------------------------*/
		//���ϻ���
		/*--------------------------------------------------*/
		//DRPG ����, added 07.11.15
		//���ҽ�-PG���� ����(������->IP), edited 10.09.09
		if( $this->m_type == TYPE_SECUREPAY )
		{
			if( $this->m_REQUEST["pgn"] == "" )
					$host = $this->m_Data->m_PG1;
			else
					$host = $this->m_REQUEST["pgn"];
		}
		else
		{
			if( $this->m_REQUEST["pgn"] == "" )
			{
				if( $this->m_cancelRC == 1 )
					$host = DRPG_IP;
				else
					$host = PG_IP;
			}
			else
					$host = $this->m_REQUEST["pgn"];
		}

		$this->m_Socket = new INISocket($host);
		if( ($rtv = $this->m_Socket->DNSLookup()) != OK )
		{
			$err_msg = "[".$host."]DNS LOOKUP ����(MAIN)".$this->m_Socket->getErr();
			$this->m_Log->WriteLog( ERROR, $err_msg );
			$this->MakeTXErrMsg( $rtv, $err_msg ); 
			if( $this->m_type == TYPE_SECUREPAY ) //PI�ϰ���, PI�� �����ִ� pg1ip��!
		  {
				$this->m_Socket->ip = $this->m_Data->m_PG1IP;
			}
			else 
			{
				if( $this->m_cancelRC == 1 )
					$this->m_Socket->ip = DRPG_IP;
				else
					$this->m_Socket->ip = PG_IP;
			}
		}
		$this->m_Log->WriteLog( INFO, "DNS LOOKUP OK(".$this->m_Socket->host.":".$this->m_Socket->ip.":".$this->m_Socket->port.") laptime:".$this->m_Socket->dns_laptime );
		if( ($rtv = $this->m_Socket->open()) != OK )
		{
			$this->m_Socket->close();

			//PG2�� ��ȯ
			$err_msg = "[".$host."���Ͽ�������(MAIN)::PG2�� ��ȯ";
			$this->m_Log->WriteLog( ERROR, $err_msg );
			$this->MakeTXErrMsg( $rtv, $err_msg ); 
			if( $this->m_type == TYPE_SECUREPAY )
			{
					$host = $this->m_Data->m_PG2;
			}
			else
			{
					$host = DRPG_HOST;
			}
			$this->m_Socket = new INISocket($host);
			if( ($rtv = $this->m_Socket->DNSLookup()) != OK )
			{
				$err_msg = "[".$host."]DNS LOOKUP ����(MAIN)".$this->m_Socket->getErr();
				$this->m_Log->WriteLog( ERROR, $err_msg );
				$this->MakeTXErrMsg( $rtv, $err_msg ); 
				if( $this->m_type == TYPE_SECUREPAY ) //PI�ϰ���, PI�� �����ִ� pg2ip��!
		  	{
					$this->m_Socket->ip = $this->m_Data->m_PG2IP;
				}
				else 
				{
					$this->m_Socket->ip = DRPG_IP;
				}
			}
			$this->m_Log->WriteLog( INFO, "DNS LOOKUP OK(".$this->m_Socket->host.":".$this->m_Socket->ip.":".$this->m_Socket->port.") laptime:".$this->m_Socket->dns_laptime );
			if( ($rtv = $this->m_Socket->open()) != OK )
			{
				$err_msg = "[".$host."���Ͽ�������(MAIN)::".$this->m_Socket->getErr();
				$this->m_Log->WriteLog( ERROR, $err_msg );
				$this->MakeTXErrMsg( $rtv, $err_msg ); 
				$this->m_Log->CloseLog( $this->GetResult(NM_RESULTMSG) );
				$this->m_Socket->close();
				$this->m_Crypto->FreeAllKey();
				return;
			}
		}
		$this->m_connIP = $this->m_Socket->ip;
		$this->m_Log->WriteLog( INFO, "SOCKET CONNECT OK" );

		/*--------------------------------------------------*/
		//�����۽�
		/*--------------------------------------------------*/
		if( ($rtv = $this->m_Socket->send($this->m_Data->m_sMsg)) != OK ) 
		{
			$err_msg = "���ϼ۽ſ���(MAIN)::".$this->m_Socket->getErr();
			$this->m_Log->WriteLog( ERROR, $err_msg );
			$this->MakeTXErrMsg( $rtv, $err_msg ); 
			$this->m_Log->CloseLog( $this->GetResult(NM_RESULTMSG) );
			$this->m_Crypto->FreeAllKey();
			$this->m_Socket->close();
			return;
		}
		$this->m_Log->WriteLog( INFO, "SEND OK" );

		/*--------------------------------------------------*/
		//��������
		/*--------------------------------------------------*/
		if( ($rtv = $this->m_Socket->recv($head, $body, $tail)) != OK ) 
		{
			$err_msg = "���ϼ��ſ���(MAIN)::".$this->m_Socket->getErr();
			$this->m_Log->WriteLog( ERROR, $err_msg );
			$this->MakeTXErrMsg( $rtv, $err_msg ); 
			$this->m_Socket->close();
			$this->NetCancel();
			$this->m_Log->CloseLog( $this->GetResult(NM_RESULTMSG) );
			$this->m_Crypto->FreeAllKey();
			return;
		}
		$this->m_Log->WriteLog( INFO, "RECV OK" );
		$this->m_Log->WriteLog( INFO, "MSG_FROM_PG:[".$head.$body.$tail."]" );
		$this->m_Data->m_Body = $body;

		/*--------------------------------------------------*/
		//����Ȯ��
		/*--------------------------------------------------*/
		if( ($rtv = $this->m_Crypto->Verify( $body, $tail )) != OK )
		{
			$err_msg = "VERIFY FAIL";
			$this->m_Log->WriteLog( ERROR, $err_msg );
			$this->MakeTXErrMsg( $rtv, $err_msg ); 
			$this->m_Socket->close();
			$this->NetCancel();
			$this->m_Log->CloseLog( $this->GetResult(NM_RESULTMSG) );
			$this->m_Crypto->FreeAllKey();
			return;
		}
		$this->m_Log->WriteLog( INFO, "VERIFY OK" );

		/*--------------------------------------------------*/
		//Head �Ľ�
		/*--------------------------------------------------*/
		if( ($rtv = $this->m_Data->ParseHead( $head )) != OK )
		{
			$err_msg = "��������(HEAD) �Ľ� ����";
			$this->m_Log->WriteLog( ERROR, $err_msg );
			$this->MakeTXErrMsg( $rtv, $err_msg ); 
			$this->m_Socket->close();
			$this->NetCancel();
			$this->m_Log->CloseLog( $this->GetResult(NM_RESULTMSG) );
			$this->m_Crypto->FreeAllKey();
			return;
		}
		$this->m_Log->WriteLog( INFO, "PARSE HEAD OK" );

		/*--------------------------------------------------*/
		//Body �Ľ�
		/*--------------------------------------------------*/
		if( ($rtv = $this->m_Data->ParseBody( $body, $encrypted, $sessionkey )) != OK )
		{
			$err_msg = "��������(Body) �Ľ� ����";
			$this->m_Log->WriteLog( ERROR, $err_msg );
			$this->MakeTXErrMsg( $rtv, $err_msg ); 
			$this->m_Socket->close();
			$this->NetCancel();
			$this->m_Log->CloseLog( $this->GetResult(NM_RESULTMSG) );
			$this->m_Crypto->FreeAllKey();
			return;
		}
		$this->m_Log->WriteLog( INFO, "PARSE BODY OK" );

		/*--------------------------------------------------*/
		//��ȣȭ
		/*--------------------------------------------------*/
  	if( $this->m_type == TYPE_SECUREPAY   || $this->m_type == TYPE_FORMPAY  || $this->m_type == TYPE_OCBSAVE  || 
      	$this->m_type == TYPE_CANCEL      || $this->m_type == TYPE_AUTHBILL || $this->m_type == TYPE_FORMAUTH || 
      	$this->m_type == TYPE_REQREALBILL || $this->m_type == TYPE_REPAY    || $this->m_type == TYPE_VACCTREPAY    || $this->m_type == TYPE_RECEIPT	||
      	$this->m_type == TYPE_AUTH       	|| $this->m_type == TYPE_CAPTURE 	|| $this->m_type == TYPE_ESCROW		||
				$this->m_type == TYPE_REFUND || $this->m_type == TYPE_INQUIRY
    	)
		{
			if( ($rtv = $this->m_Crypto->Decrypt( $sessionkey, $encrypted, $decrypted )) != OK )
			{
				$err_msg = "��ȣȭ ����[".$this->GetResult(NM_RESULTMSG)."]";
				$this->m_Log->WriteLog( ERROR, $err_msg );
				$this->MakeTXErrMsg( $rtv, $err_msg ); 
				$this->m_Socket->close();
				$this->NetCancel();
				$this->m_Log->CloseLog( $this->GetResult(NM_RESULTMSG) );
				$this->m_Crypto->FreeAllKey();
				return;
			}
			$this->m_Log->WriteLog( INFO, "DECRYPT OK" );
			$this->m_Log->WriteLog( DEBUG, "DECRYPT MSG:[".$decrypted."]" );

			//Parse Decrypt
			$this->m_Data->ParseDecrypt( $decrypted );
			$this->m_Log->WriteLog( INFO, "DECRYPT PARSE OK" );
		}

		/*--------------------------------------------------*/
		//Assign Interface Variables
		/*--------------------------------------------------*/
		$this->m_RESULT					=	$this->m_Data->m_RESULT;

		/*--------------------------------------------------*/
		//ACK
		/*--------------------------------------------------*/
		//if( $this->GetResult(NM_RESULTCODE) == "00" && 
		if( (strcmp($this->GetResult(NM_RESULTCODE),"00") == 0) && 
      ( $this->m_type == TYPE_SECUREPAY || $this->m_type == TYPE_OCBSAVE || 
        $this->m_type == TYPE_FORMPAY   || $this->m_type == TYPE_RECEIPT
      )
		)
		{
			$this->m_Log->WriteLog( INFO, "WAIT ACK INVOKING" );
			if( ($rtv = $this->Ack()) != OK )
			{
				//ERROR
				$err_msg = "ACK ����";
				$this->m_Log->WriteLog( ERROR, $err_msg );
				$this->MakeTXErrMsg( $rtv, $err_msg ); 
				$this->m_Socket->close();
				$this->NetCancel();
				$this->m_Log->CloseLog( $this->GetResult(NM_RESULTMSG) );
				$this->m_Crypto->FreeAllKey();
				return;
			}
			$this->m_Log->WriteLog( INFO, "SUCCESS ACK INVOKING" );
		}
		/*--------------------------------------------------*/
		//PG ����Ű�� �ٲ������� ����Ű UPDATE
		/*--------------------------------------------------*/
		$pgpubkey = $this->m_Data->GetXMLData( NM_PGPUBKEY );
		if( $pgpubkey != "" )
		{
			if( ($rtv = $this->m_Crypto->UpdatePGPubKey( $pgpubkey )) != OK )
			{
					$err_msg = "PG����Ű ������Ʈ ����";
					$this->m_Log->WriteLog( ERROR, $err_msg );
					$this->m_Data->GTHR( $rtv, $err_msg );
			}
			else
				$this->m_Log->WriteLog( INFO, "PGPubKey UPDATED!!" );
		}

		$this->m_Log->CloseLog( $this->GetResult(NM_RESULTMSG) );
		$this->m_Crypto->FreeAllKey();
		$this->m_Socket->close();

		/*--------------------------------------------------*/
		//���ҽ���-���ŷ������ÿ� DRPG�� ���õ�
		//2008.04.01
		/*--------------------------------------------------*/
		if( $this->GetResult(NM_RESULTCODE) == "01" && ($this->m_type == TYPE_CANCEL || $this->m_type == TYPE_INQUIRY) && $this->m_cancelRC == 0 )
		{
				if( intval($this->GetResult(NM_ERRORCODE)) > 400000 && substr( $this->GetResult(NM_ERRORCODE), 3, 3 ) == "623" )	
				{
					$this->m_cancelRC = 1;
					$this->startAction();
				}
		}

		return;

	} // End of StartAction

	/*--------------------------------------------------*/
	/*																									*/
	/* �������� ������ ������ ����Ÿ ����								*/
	/*																									*/
 	/*--------------------------------------------------*/
	function MakeChkFake()
	{
			$this->m_Crypto = new INICrypto( $this->m_REQUEST );

			/*--------------------------------------------------*/
			//��������Ű �ε�
			/*--------------------------------------------------*/
			if( ($rtv = $this->m_Crypto->LoadMPrivKey()) != OK ) 
			{
				$err_msg = "��������Ű �ε�����";
				$this->m_Log->WriteLog( ERROR, $err_msg );
				$this->MakeTXErrMsg( $rtv, $err_msg ); 
				$this->m_Log->CloseLog( $this->GetResult(NM_RESULTMSG) );
				$this->m_Crypto->FreePubKey();
				return;
			}
			$this->m_Log->WriteLog( INFO, "MERCHANT PRIV KEY LOAD OK" );

			/*--------------------------------------------------*/
			//���� ����Ű �ε�(SN �� �˱�����!!)
			/*--------------------------------------------------*/
			if( ($rtv = $this->m_Crypto->LoadMPubKey( $m_cert_SN )) != OK)
			{
				$err_msg = "��������Ű �ε�����";
				$this->m_Log->WriteLog( ERROR, $err_msg );
				$this->MakeTXErrMsg( $rtv, $err_msg ); 
				$this->m_Log->CloseLog( $this->GetResult(NM_RESULTMSG) );
				return;
			}
			$this->m_Log->WriteLog( INFO, "MERCHANT PUB KEY LOAD OK [".$this->m_Data->m_MPubSN."]" );

      foreach ($this->m_REQUEST as $key => $val)
      {
				if( $key == "inipayhome" || $key == "type" || $key == "debug" || 
						$key == "admin" || $key == "checkopt" || $key == "enctype" )
					continue;
				if( $key == "mid" ) 
					$temp1 .= $key."=".$val."&"; //msg
				else
					$temp2 .= $key."=".$val."&"; //hashmsg
      }
			//Make RN
			$this->m_RESULT["rn"] = $this->m_Data->MakeRN();
			$temp1 .= "rn=".$this->m_RESULT["rn"]."&";

			$checkMsg = $temp1;
			$checkHashMsg = $temp2;

			$retHashStr = Base64Encode(sha1( $checkHashMsg, TRUE ));
			$checkMsg .= "data=".$retHashStr;

			$HashMid = Base64Encode(sha1( $this->m_REQUEST["mid"], TRUE ));

			$this->m_Crypto->RSAMPrivEncrypt( $checkMsg, $RSATemp );
			$this->m_RESULT["encfield"] = "enc=".$RSATemp."&src=".Base64Encode($checkHashMsg);
			$this->m_RESULT["certid"] = $HashMid.$m_cert_SN;

			$this->m_Log->WriteLog( INFO, "CHKFAKE KEY MAKE OK:".$this->m_RESULT["rn"] );

			$this->m_Log->CloseLog( $this->GetResult(NM_RESULTMSG) );
			$this->m_Crypto->FreeAllKey();
			$this->m_RESULT[NM_RESULTCODE] = "00";
			return;
	}

	/*--------------------------------------------------*/
	/*																									*/
	/* ����ó�� Ȯ�� �޼��� ����												*/
	/*																									*/
 	/*--------------------------------------------------*/
	function Ack()
	{
			//ACK�� Data	
			$this->m_Data->m_sBody = "";
			$this->m_Data->m_sTail = "";
			$this->m_Data->m_sCmd = CMD_REQ_ACK;

			//��������(Head)
			$this->m_Data->MakeHead();
			$this->m_Log->WriteLog( DEBUG, "MAKE HEAD OK" );
			//$this->m_Log->WriteLog( DEBUG, "MSG_TO_PG:[".$this->m_Data->m_sMsg."]" );

			//Send
			if( ($rtv = $this->m_Socket->send($this->m_Data->m_sMsg)) != OK ) 
			{
				$err_msg = "ACK ���ۿ���";
				$this->m_Log->WriteLog( ERROR, $err_msg );
				return ACK_CHECKSUM_ERR;
			}
			//$this->m_Log->WriteLog( DEBUG, "SEND OK" );

			if( ($rtv = $this->m_Socket->recv($head, $body, $tail)) != OK ) 
			{
				$err_msg = "ACK ���ſ���(ACK)";
				$this->m_Log->WriteLog( ERROR, $err_msg );
				return ACK_CHECKSUM_ERR;
			}
			//$this->m_Log->WriteLog( DEBUG, "RECV OK" );
			//$this->m_Log->WriteLog( INFO, "MSG_FROM_PG:[".$recv."]" );
			return OK;
	}

	/*--------------------------------------------------*/
	/*																									*/
	/* ������ �޼��� ����																*/
	/*																									*/
 	/*--------------------------------------------------*/
	function NetCancel()
	{
			$this->m_Log->WriteLog( INFO, "WAIT NETCANCEL INVOKING" );

     	if ( $this->m_type == TYPE_CANCEL || $this->m_type == TYPE_REPAY || $this->m_type == TYPE_VACCTREPAY || $this->m_type == TYPE_RECEIPT || 
					 $this->m_type == TYPE_CONFIRM || $this->m_type == TYPE_OCBQUERY || $this->m_type == TYPE_ESCROW  || 
					 $this->m_type == TYPE_CAPTURE || $this->m_type == TYPE_AUTH || $this->m_type == TYPE_AUTHBILL  ||
      		 ($this->m_type == TYPE_ESCROW && $this->m_Data->m_EscrowType == TYPE_ESCROW_DNY_CNF ) ||
					 $this->m_type == TYPE_NETCANCEL
				)
			{
				$this->m_Log->WriteLog( INFO, "DON'T NEED NETCANCEL" );
				return true;
			}

			//NetCancel�� Data	
      $this->m_Data->m_REQUEST["cancelmsg"] = "������";
			$body = "";
			$sign = "";

			$this->m_Data->m_Type = TYPE_CANCEL; //������ ������ ���������� ����.������Ʋ����..��~

			//added escrow netcancel, 08.03.11
			if( $this->m_type == TYPE_ESCROW && $this->m_Data->m_EscrowType == TYPE_ESCROW_DLV ) 
				$this->m_Data->m_sCmd = CMD_REQ_DLV_NETC;
      else if($this->m_type == TYPE_ESCROW && $this->m_Data->m_EscrowType == TYPE_ESCROW_CNF )
				$this->m_Data->m_sCmd = CMD_REQ_CNF_NETC;
      else if($this->m_type == TYPE_ESCROW && $this->m_Data->m_EscrowType == TYPE_ESCROW_DNY )
				$this->m_Data->m_sCmd = CMD_REQ_DNY_NETC;
			else
				$this->m_Data->m_sCmd = CMD_REQ_NETC;

			$this->m_Data->m_sCrypto	= FLAG_CRYPTO_3DES;

			//��ȣȭ
			if( ($rtv = $this->m_Data->MakeEncrypt( $this->m_Crypto )) != OK )
			{
				$err_msg = "��ȣȭ ����";
				$this->m_Log->WriteLog( ERROR, $err_msg );
				//$this->MakeTXErrMsg( $rtv, $err_msg ); 
				return;
			}
			$this->m_Log->WriteLog( DEBUG, "MAKE ENCRYPT OK[".$this->m_Data->m_EncBody."]" );

			//��������(Body)
			$this->m_Data->MakeBody();
			$this->m_Log->WriteLog( INFO, "MAKE BODY OK" );
	
			//����(sign)
			if( ($rtv = $this->m_Crypto->Sign( $this->m_Data->m_sBody, $sign )) != OK )
			{
				$err_msg = "���ν���";
				$this->m_Log->WriteLog( ERROR, $err_msg );
				//$this->MakeTXErrMsg( $rtv, $err_msg ); 
				return false;
			}
			$this->m_Data->m_sTail = $sign;
			$this->m_Log->WriteLog( INFO, "SIGN OK" );
	
			//��������(Head)
			$this->m_Data->MakeHead();
			$this->m_Log->WriteLog( INFO, "MAKE HEAD OK" );
	
			$this->m_Log->WriteLog( DEBUG, "MSG_TO_PG:[".$this->m_Data->m_sMsg."]" );

			//���ϻ���
			$this->m_Socket = new INISocket("");
			$this->m_Socket->ip = $this->m_connIP; //���������� IP ����, 08.03.12
			if( ($rtv = $this->m_Socket->open()) != OK )
			{
				$err_msg = "[".$this->m_Socket->ip."]���Ͽ�������(NETC)::".$this->m_Socket->getErr();
				$this->m_Log->WriteLog( ERROR, $err_msg );
				//$this->MakeTXErrMsg( $rtv, $err_msg ); 
				$this->m_Log->CloseLog( $this->GetResult(NM_RESULTMSG) );
				$this->m_Socket->close();
				$this->m_Crypto->FreeAllKey();
				return;
			}
			$this->m_Log->WriteLog( INFO, "SOCKET CONNECT OK::".$this->m_Socket->ip );

			//�����۽�
			if( ($rtv = $this->m_Socket->send($this->m_Data->m_sMsg)) != OK ) 
			{
				$err_msg = "���ϼ۽ſ���(NETC)".$this->m_Socket->getErr();
				$this->m_Log->WriteLog( ERROR, $err_msg );
				//$this->MakeTXErrMsg( $rtv, $err_msg ); 
				$this->m_Socket->close();
				return false;
			}
			$this->m_Log->WriteLog( INFO, "SEND OK" );
	
			//��������
			if( ($rtv = $this->m_Socket->recv($head, $body, $tail)) != OK ) 
			{
				$err_msg = "���ϼ��ſ���(NETC)";
				$this->m_Log->WriteLog( ERROR, $err_msg );
				//$this->MakeTXErrMsg( $rtv, $err_msg ); 
				$this->m_Socket->close();
				return false;
			}
			$this->m_Log->WriteLog( INFO, "RECV OK" );
			$this->m_Log->WriteLog( DEBUG, "MSG_FROM_PG:[".$head.$body.$tail."]" );
	
			//����Ȯ��
			if( ($rtv = $this->m_Crypto->Verify( $body, $tail )) != OK )
			{
				$err_msg = "VERIFY FAIL";
				$this->m_Log->WriteLog( ERROR, $err_msg );
				//$this->MakeTXErrMsg( $rtv, $err_msg ); 
				$this->m_Socket->close();
				return false;
			}
			$this->m_Log->WriteLog( INFO, "VERIFY OK" );

			//���� ������ ������ �Ľ����� �ʴ´�!!!!
			//�׳� ���⼭ ������ �ǰ��ϴ�.-_-;;
			//Head �Ľ�
			if( ($rtv = $this->m_Data->ParseHead( $head )) != OK )
			{
				$err_msg = "��������(HEAD) �Ľ� ����";
				$this->m_Log->WriteLog( ERROR, $err_msg );
				//$this->MakeTXErrMsg( $rtv, $err_msg ); 
				$this->m_Socket->close();
				return;
			}
			//Body �Ľ�
			if( ($rtv = $this->m_Data->ParseBody( $body, $encrypted, $sessionkey )) != OK )
			{
				$err_msg = "��������(Body) �Ľ� ����";
				$this->m_Log->WriteLog( ERROR, $err_msg );
				//$this->MakeTXErrMsg( $rtv, $err_msg ); 
				$this->m_Socket->close();
				return;
			}

			//if( $this->GetResult(NM_RESULTCODE) == "00" )
			if(strcmp($this->GetResult(NM_RESULTCODE),"00") == 0)
				$this->m_Log->WriteLog( INFO, "SUCCESS NETCANCEL" );
			else
				$this->m_Log->WriteLog( ERROR, "ERROR NETCANCEL[".$this->GetResult(NM_RESULTMSG)."]" );
			return true;
	}
		
	function MakeIMStr($s, $t)
	{
		$this->m_Crypto = new INICrypto( $this->m_REQUEST );
		if( $t == "H" )
			return $this->m_Crypto->MakeIMStr($s, base64_decode(IMHK));
		else if( $t == "J" )
			return $this->m_Crypto->MakeIMStr($s, base64_decode(IMJK));
	}

	/*--------------------------------------------------*/
	/*																									*/
	/* �����޼��� Make				                          */
	/*																									*/
 	/*--------------------------------------------------*/
	function MakeTXErrMsg($err_code, $err_msg)
	{
		$this->m_RESULT[NM_RESULTCODE]			= "01";
		$this->m_RESULT[NM_RESULTERRORCODE]	= $err_code;
		$this->m_RESULT[NM_RESULTMSG]				= "[".$err_code."|".$err_msg."]";
		$this->m_Data->GTHR( $err_code, $err_msg );
		return;
	}

}

?>
