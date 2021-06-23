<?php
 if(!defined('EZTPL')){
 die('Forbidden access');
}
?>
<?php if(isset($this->eztpl_vars['res']) && is_array($this->eztpl_vars['res'])){ ?><?php foreach($this->eztpl_vars['res'] as $key=>$value){ ?><?php echo $key;?>:<?php echo $value;?><br /><?php } ?><?php
}else{
?>没有可以循环的值<?php } ?>