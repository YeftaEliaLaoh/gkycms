<?php
class pdftable {

    function __construct() {
        include APPPATH . '/third_party/fpdf/fpdf.php';
        include APPPATH . '/third_party/fpdf/exfpdf.php';
        include APPPATH . '/third_party/fpdf/easyTable.php';
        //include APPPATH . '/third_party/fpdf/Blt.php';
    }
}
?>