<?php
//include_once('../pdf16/fpdf.php');

//Stream handler to read from global variables
class VariableStream
{
	var $varname;
	var $position;

	function stream_open($path, $mode, $options, &$opened_path)
	{
		$url = parse_url($path);
		$this->varname = $url['host'];
		if(!isset($GLOBALS[$this->varname]))
		{
			trigger_error('Global variable '.$this->varname.' does not exist', E_USER_WARNING);
			return false;
		}
		$this->position = 0;
		return true;
	}

	function stream_read($count)
	{
		$ret = substr($GLOBALS[$this->varname], $this->position, $count);
		$this->position += strlen($ret);
		return $ret;
	}

	function stream_eof()
	{
		return $this->position >= strlen($GLOBALS[$this->varname]);
	}

	function stream_tell()
	{
		return $this->position;
	}

	function stream_seek($offset, $whence)
	{
		if($whence==SEEK_SET)
		{
			$this->position = $offset;
			return true;
		}
		return false;
	}
	
	function stream_stat()
	{
		return array();
	}
}

class PDF_MemImage extends FPDF {
	//------------INCRUSTAMOS FUNCIONES DE ALPHA ABC
	var $extgstates = array();
	function SetAlpha($alpha, $bm='Normal') {
		// set alpha for stroking (CA) and non-stroking (ca) operations
		$gs = $this->AddExtGState(array('ca'=>$alpha, 'CA'=>$alpha, 'BM'=>'/'.$bm));
		$this->SetExtGState($gs);
	}

	function AddExtGState($parms) {
		$n = count($this->extgstates)+1;
		$this->extgstates[$n]['parms'] = $parms;
		return $n;
	}

	function SetExtGState($gs) {
		$this->_out(sprintf('/GS%d gs', $gs));
	}

	function _enddoc() {
		if(!empty($this->extgstates) && $this->PDFVersion<'1.4')
			$this->PDFVersion='1.4';
		parent::_enddoc();
	}

	function _putextgstates() {
		for ($i = 1; $i <= count($this->extgstates); $i++) {
			$this->_newobj();
			$this->extgstates[$i]['n'] = $this->n;
			$this->_out('<</Type /ExtGState');
			foreach ($this->extgstates[$i]['parms'] as $k=>$v)
				$this->_out('/'.$k.' '.$v);
			$this->_out('>>');
			$this->_out('endobj');
		}
	}

	function _putresourcedict() {
		parent::_putresourcedict();
		$this->_out('/ExtGState <<');
		foreach($this->extgstates as $k=>$extgstate)
			$this->_out('/GS'.$k.' '.$extgstate['n'].' 0 R');
		$this->_out('>>');
	}

	function _putresources() {
		$this->_putextgstates();
		parent::_putresources();
	}
	
	//----------------------------------------------

	function PDF_MemImage($orientation='P', $unit='mm', $format='A4') {
		$this->FPDF($orientation, $unit, $format);
		//Register var stream protocol
		stream_wrapper_register('var', 'VariableStream');
	}

	function MemImage($data, $x=null, $y=null, $w=0, $h=0, $link='') {
		//Display the image contained in $data
		$v = 'img'.md5($data);
		$GLOBALS[$v] = $data;
		$a = getimagesize('var://'.$v);
		if(!$a)
			$this->Error('Invalid image data');
		$type = substr(strstr($a['mime'],'/'),1);
		$this->Image('var://'.$v, $x, $y, $w, $h, $type, $link);
		unset($GLOBALS[$v]);
	}

	function GDImage($im, $x=null, $y=null, $w=0, $h=0, $link='') {
		//Display the GD image associated to $im
		ob_start();
		imagepng($im);
		$data = ob_get_clean();
		$this->MemImage($data, $x, $y, $w, $h, $link);
	}
}
?>
