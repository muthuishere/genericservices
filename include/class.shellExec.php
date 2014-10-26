<?php
class shellExec{
  var $commandLine;
  var $resultCode = 0;
  var $output = array();
 
  public static function withCommandLine($commandLine){
    return new shellExec($commandLine);
  }
 
  function __construct($commandLine)
  {
    $this->commandLine = $commandLine;
  }
 
  public function launch()
  {
    exec($this->commandLine, $this->output, $this->resultCode);

    return $this;
  }
  public function execute($command){
  
	$pid = popen( $command,"r");
	 
	
	while( !feof( $pid ) )
	{
	 echo fread($pid, 256);
	 flush();
	 ob_flush();
	// echo "<script>window.scrollTo(0,99999);</script>";
	 usleep(100000);
	}
	pclose($pid);

  
  }
 
  public function getCommandLine()
  {
    return $this->commandLine;
  }
 
  public function getResultCode()
  {
    return $this->resultCode;
  }
 
  public function getOutput()
  {
    return $this->output;
  }
}