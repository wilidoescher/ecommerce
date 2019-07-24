<?php
namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;
class User extends Model
{
	const SESSION = "User";
	const SECRET = "HcodePhp7_Secret";
	public static function checkLogin($inadmin = true)
	{
		if (
			!isset($_SESSION[User::SESSION])
			||
			!$_SESSION[User::SESSION]
			||
			!(int)$_SESSION[User::SESSION]["iduser"] > 0
		) {
			//Não está logado
			return false;
		} else {
			if ($inadmin === true && (bool)$_SESSION[User::SESSION]['inadmin'] === true) {
				return true;

			} else if ($inadmin === false) {
				return true;

			} else {
				return false;

			}
		}
	}
	public static function login($login, $password)
	{
		$sql = new Sql();
		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b ON a.idperson = b.idperson WHERE a.deslogin = :LOGIN", array(
			":LOGIN"=>$login
		)); 

		if (count($results) === 0) {

			throw new \Exception("Usuário inexistente ou senha inválida.");
			
		}
		$data = $results[0];

		if (password_verify($password, $data["despassword"]) === true)
		{
			$user = new User();

			$data['desperson'] = utf8_encode($data['desperson']);

			$user->setData($data);

			$_SESSION[User::SESSION] = $user->getValues();

			return $user;

		} else {
			throw new \Exception("Usuário inexistente ou senha inválida.");
		}
	}
	public static function verifyLogin($inadmin = true)
	{
		if (!User::checkLogin($inadmin)) {

			if ($inadmin) {
				header("Location: /admin/login");
			} else {
				header("Location: /login");
			}
			exit;
		}
	}
	public static function logout() //LOGOUT
	{
		$_SESSION[User::SESSION] = NULL;
	}
	public static function listALL() //LISTA USUARIOS
	{
		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");
	}
	public function save()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",array(
			":desperson"=>$this->getdesperson(),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>$this->getdespassword(),
			"desemail"=>$this->getdesemail(),
			"nrphone"=>$this->getnrphone(),
			"inadmin"=>$this->getinadmin()

		));

		$this->setData($results[0]);
	}

	public function get($iduser) 
	{	 
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser;", array(		 
			":iduser" => $iduser		 
		));

		$data = $results[0];

		$this->setData($data);	
	}

	public function update()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
			":iduser"=>$this->getiduser(),
			":desperson"=>$this->getdesperson(),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>$this->getdespassword(),
			"desemail"=>$this->getdesemail(),
			"nrphone"=>$this->getnrphone(),
			"inadmin"=>$this->getinadmin()

		));

		$this->setData($results[0]);

	}

}


?>