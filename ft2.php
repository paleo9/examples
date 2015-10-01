<?php

function str_nsplit( $s, $pos)
{
    echo "<p>In str_nsplit";
    $a = array();
    $start = 0;
    for ($i = 0; $i < count($pos); $i++) {
	$end = $pos[$i]; 
	$a[$i] = substr($s, $start, $end);
	$start += $end;
    }
    return $a;
}

function arr_pack($arr, $pos) {
    $str = "";
    for($i=0; $i<count($arr); $i++) {
	$str  = $str . str_pad($arr[$i], $pos[$i], 'x');
    }
    return $str;
}

/*
SI_FileFormat

From the Spark Response Application Integration Specfications pdf, enter the 
Field Name and numbers from the Format into constructor's $headings and 
$positions arguments respectively.

Pack creates a string from an array. 

Unpack uses $position to determine the length of each substring which will 
be created and returned, one per element of the returned array. 

*/

class SI_FileLineFormat {
    var $headings;
    var $positions;

    function __construct() {
	echo "<p>Woohoo from SI_FileLIneFormat";
    }

    function init($headings, $positions) {
	echo "<p>In init";
	$this->headings = $headings;
	$this->positions = $positions;
    }

    function pack($arr) {
	return implode($arr);
    }

    function unpack($str) {
	echo "<p> in SI_FileLIneFormat-unpack";
	$values = str_nsplit($str, $this->positions);
	return array_combine($this->headings, $values);
    }

    function dump() {
	echo "<p>Dumping";
	echo "<p>There are " . count($this->positions) ." positions and " . count($this->headings) ." headings.";
	echo"<p>Positions:";
	foreach($this->positions as $k=>$v) {
	    echo ", $v";
	}
	echo"<p>Headings: ";
	foreach($this->headings as $k=>$v) {
	    echo ", $v";
	}
	echo "<p>End of dump";
    }

}

class SI_Rec {
    var $format;  //
    var $lines;
    
    function __construct() {
	 echo "<p>WooHoo from SI_Rec";
	 $this->format = new SI_FileLineFormat();
	 $this->lines = array();
    }

    function pack($arr) {
	return implode($arr);
    }

    function unpack($s) {
	echo "<p>In SI_Rec->unpack";
	$arr = $this->format->unpack($s);
	echo "<p>end of  SI_Rec->unpack";
	return $arr;
    }

    function push_string($s) {
	echo "<p>in push_string";
	$arr = $this->unpack($s);
	if ($arr){
	    $this->push_array($arr);
	}
    }

    function push_array($arr) {
	$str = $this->pack($arr);
	echo "<p>pushing $str";
	$this->lines[] = $this->pack($arr);
    }

    function dump() {
	echo "<h1>File dump</h1>";
	echo "<ul>";
	foreach ($this->lines as $v) {
	    echo "<li>$v";
	}
	echo "</ul>";
    }
}

/*               ***  INVENTORY LEVELS *** */
/* HEADER */
class SI_IF001_InventoryLevels_Header extends SI_Rec {
    function __construct() {
echo "<p>InventoryLevelsHeader constructor";
	parent::__construct();
	$header_names = ['InterfaceID', 'BatchNumber', 'InterfaceVersion', 'Orders'];
	$header_positions = [7,9,4,5];
	$this->format->init($header_names, $header_positions);
    }
}

/* LINE */  
class SI_IF001_InventoryLevels_Product extends SI_Rec {
    function __construct() {
	parent::__construct();
	$line_names = ['recordid', 'itemno', 'description', 'status', 
	    'availableinv', 'inprocess', 'backorders', 'physicalusable', 
	    'physicalnonusable', 'inwarehouse', 'totalduein', 'Weight', 'PONumber1', 
	    'PONumber2', 'PONumber3', 'PONumber4', 'PONumber5', 'NextQty1', 'NextQty2',
	    'NextQty3', 'NextQty4', 'NextQty5', 'ExpectedDate1', 'ExpectedDate2',
	    'ExpectedDate3', 'ExpectedDate4', 'ExpectedDate5'];

	$line_positions = [7,20,50,2,5,5,5,5,5,5,5,10,10,10,10,10,10,9,9,9,
			    9,9,8,8,8,8,8];
	$this->format->init($line_names, $line_positions);
    }
}

/*         *********** IF013 SALES ORDER ITEM STATUS   ************/

/* HEADER */
class SI_IF013_SO_ItemStatus_Header extends SI_Rec {
    function __construct() {
	parent::__construct();
	$header_names = ['InterfaceID', 'BatchNumber', 'InterfaceVersion', 'Records'];
	$header_positions = [7,9,4,5];
	$this->format->init($header_names, $header_positions);
    }
}

/* LINE */  
class SI_IF013_ItemStatus_Transaction extends SI_Rec {
    function __construct() {
	parent::__construct();
	$line_positions = [ 7,12,24,4,20, 1,4,5,30,8, 20,20,30,30 ];
	$line_names = 
	    [
	    'InterfaceID', 'FullOrderNo', 'OrderXref', 'LineNo', 'ItemNo', 
	    'Status', 'Status Priority', 'Quantity', 'SerialNo', 'TransactionDate',
	    'ShipMethod', 'Carrier', 'CarrierService', 'TrackingReference'
	    ];
	$this->format->init($line_names, $line_positions);
    }
}



/*         *********** IF011 ORDER   ************/

/* BATCH HEADER */
class SI_IF011_Order_BatchHeader extends SI_Rec {
    function __construct() {
	parent::__construct();
	$header_names = ['InterfaceID', 'BatchNumber', 'InterfaceVersion', 'Orders'];
	$header_positions = [7,9,4,5];
	$this->format->init($header_names, $header_positions);
    }
}

/* ORDER HEADER */  
class SI_IF011_Order_OrderHeader extends SI_Rec {
    function __construct() {
	parent::__construct();
	$line_positions = [ 7,20,24,20,6, 8,1,1,7,1, 11,30,3,11,11,
			    20,2,8,2,1, 16,20,4,6,2, 4,8 ];	
	$line_names = [ 'RecordID', 'OrderNo', 'OrderXref', 'PurchaseOrderRef', 'Filler1',
			'OrderDate', 'ShipComplete', 'HoldReason', 'Filler2', 'DiscountCode',
			'DiscountAmount', 'DiscountDescription', 'Currency', 'OrderTotal', 'P&H',
			'ShipMethod', 'OrderLineStatus', 'FutureOrderDate', 'PaymentMethod', 
			'ExpediteOrder', 'Source', 'CardNo', 'ExpiryDate', 'AuthCode', 'Issue',
			'StartDate', 'ShippedDate' ];
	$this->format->init($line_names, $line_positions);
    }
}

/* SAGEPAY */	
class SI_IF011_Order_SagePay extends SI_Rec {
    function __construct() {
	parent::__construct();
	$line_positions = [
	7, 20, 10, 4, 20, 40, 38, 20, 255, 10, 50, 20, 20, 20, 1,
	50, 32, 20, 20, 15, 4,100 ];
	$line_names = [
	    'RecordID', 'OrderNo', 'SecurityKey', 'VPSProtocol', 'TxType',
	    'VendorTxCode', 'VPSTxId', 'Status', 'StatusDetail', 'TXAuthNo',
	    'AVSCV2', 'AddressResult', 'PostCodeResult', 'CV2Result', 'GiftAid',
	    '3DSecureStatus', 'CAVV', 'AddressStatus', 'PayerStatus', 'CardType',
	    'Last4Digits','VPSSignature'
	    ];
	$this->format->init($line_names, $line_positions);
    }
}

/* ADDRESS */	
class SI_IF011_Order_Address extends SI_Rec {
    function __construct() {
	parent::__construct();
	$line_positions = [ 7, 20, 24, 10, 24, 24, 50, 50, 50, 50, 50, 2, 20, 4, 50,
			    15, 15, 15, 1, 1, 1, 1 ]; 
	$line_names = [
	    'RecordID', 'OrderNo', 'CustomerNo', 'Title', 'FirstName',
	    'LastName', 'Company', 'Ref1', 'Street', 'Ref2',
	    'City', 'State', 'PostCode', 'Country', 'EmailAddress',
	    'Telephone', 'Mobile', 'Fax', 'OptEmail', 'OptPhone',
	    'OptMail', 'OptRent'
	    ];
	$this->format->init($line_names, $line_positions);
    }
}

/* ORDER COUPON */	
class SI_IF011_Order_OrderCoupon extends SI_Rec {
    function __construct() {
	parent::__construct();
	$line_positions = [ 7,20,20,2,7 ];
	$line_names = [ 'RecordID','OrderNo','CouponNo','PercentOff','AmountOff' ];
	$this->format->init($line_names, $line_positions);
    }
}

/* ORDER COMMENTS */	
class SI_IF011_Order_OrderComments extends SI_Rec {
    function __construct() {
	parent::__construct();
	$line_positions = [ 7,20,60,60,1, 1,1,1 ];
	$line_names = [ 'RecordID', 'OrderNo', 'Comment1', 'Comment2', 'PackSlip1', 
			'Label1', 'PackSlip2', 'Label2' ];
	$this->format->init($line_names, $line_positions);
    }
}

/* ORDERLINE */	
class SI_IF011_Order_OrderLine extends SI_Rec {
    function __construct() {
	parent::__construct();
	$line_positions = [ 7,20,4,20,50, 5,11,11,8,1, 5 ];
	$line_names = [ 'RecordID', 'OrderNo', 'LineNo', 'ItemNo', 'ItemDescription',
	    'Quantity', 'OriginalLinePrice', 'TotalLinePrice', 'ShippedDate', 'Cancelled',
	    'ReturnedQuantity' ];
	$this->format->init($line_names, $line_positions);
    }
}

/* ORDERLINE CUSTOMISATIONS */	
class SI_IF011_Order_OrderLineCustomisation extends SI_Rec {
    function __construct() {
	parent::__construct();
	$line_positions = [ 7,20,4,20,2, 120 ];
	$line_names = [ 'RecordID', 'OrderNo', 'LineNo', 'ItemNo', 'CustomisationNo',
			'CustomisationText' ];
	$this->format->init($line_names, $line_positions);
    }
}

/* VARIABLE KIT COMPONENTS */	
class SI_IF011_Order_VariableKitComponents extends SI_Rec {
    function __construct() {
	parent::__construct();
	$line_positions = [ 7,20,4,20,50, 5,11,11,8,1, 5,4 ];
	$line_names = [ 'RecordID', 'OrderNo', 'LineNo', 'ItemNo', 'ItemDescription',
		'Quantity', 'OriginalLinePrice', 'TotalLinePrice', 'ShippedDate', 'Cancelled',
		'ReturnedQuantity', 'Association'];
	$this->format->init($line_names, $line_positions);
    }
}


class SI_IF001_InventoryLevels {




class SI_IF011_SalesOrder {
    var $batch_header;
    var $order_header;
    var $buyer_address;
    var $delivery_address;
    var $order_comments;
    var $sagepay_results;
    var $orderlines;

    function __construct() {
	$batch_header = new SI_IF011_Order_BatchHeader();
	$order_header = new SI_IF011_Order_OrderHeader();
	$buyer_address = new SI_IF011_Order_Address();
	$delivery_address = new SI_IF011_Order_Address();
	$order_comments = new SI_IF011_Order_Comments();
	$sagepay_results = new SI_IF011_Order_SagePay();
	$orderlines = new SI_IF011_Order_OrderLine();
    }

}



// sales order file format
batch_header
order_header
buser_address
delivery_address
order_comments

order_line
order_line
order_line
order_line
order_line


/*
files 
if001, if011, if013
create header
create line


*/

/* THE ACTUAL ORDER FILE */
class  SI_FileRec {
    var $header; // SI_Rec
    var $products;  //  SI_Rec

    function __construct() {
	$this->header = new SI_Rec();
	$this->products = new SI_Rec();
    }

   function set_header($arr) {
       echo "<p>In SI_FileRec set_header";
       $this->header->push_array($arr);
       echo "<p>header sert";
   }

    function push_line($str_line) {
	$this->products->push_line($str_line);
    }

    function push_array($arr) {
	$this->products->push_array($arr);
    }

    function to_file($filename){
	echo "<p>In to_file";
	$f = fopen($filename, 'w');
	fwrite($f, $this->header->lines[0]);
	$product_lines = $this->products->lines;
	foreach($this->product_lines as $p_line) {
	    fwrite($f, $p_line);
	}
	fclose($f);
    }

}



class SI_IF001_InventoryLevels extends SI_FileRec {
    function __construct() {
	$this->header = new SI_IF001_InventoryLevels_Header();
	$this->products = new SI_IF001_InventoryLevels_Product();
    }
    
    function read_file($filename){
	echo "<p>In readfile";
	if(is_readable($filename)) {
	    echo "<p>$filename is not readable";
	} else {
	    echo "<p>$filename is NOT readable ";
	}
	$f = fopen($filename, 'r');

	echo "<p>Read " . count($f) ." lines";
	echo "<p>contents....$f[0]";
	$this->header->push_string($f[0]);
	for ($i=1; $i<count($f)-1; $i++) {
	    $this->products->push_string($f[$i]);
	}
	echo "<p>Leaving readfile";
    }

    function dump() {
	echo "<p>In dump";
	$this->header->dump();
	$this->products->dump();
    }

    function push_array($arr){
	$rec = new SI_IF001_InventoryLevels_Product();
	$str = $rec->pack($arr);
	$this->push_line($str);
    }

}


/*

read from db
create inv_header
create 1 line
*/














?>
