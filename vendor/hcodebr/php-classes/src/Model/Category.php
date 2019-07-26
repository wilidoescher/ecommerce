<?php
namespace Hcode\Model;
use \Hcode\Db\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class Category extends Model {
	
	
	public static function listAll(){
		$sql = new Sql();
		return $sql->select("SELECT * FROM tb_categories ORDER BY descategory");
	}
	

}


?>