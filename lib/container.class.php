<?php
/**
 * Teambox API Wrapper for PHP
 */

class Teambox
{
  # variables that hold information needed to construct the Teambox account
  public $accountid;
  public $username;
  public $password;
  public $app_key = 'FxzSnAyxv41iwbMZqQdlxVifZ4lzOo8ifk5wWo23';
  public $app_secret = 'GJvA1Yft7wMXsInqq4ROvPULc1lOLyEPQXaLCPfh';
  public $token_url = 'https://teambox.com/oauth/token';
  public $auth_url = 'https://teambox.com/oauth/authorize';

  # a private collection of all Teambox objects
  private $collection;

  # variables that hold information about the current api request
  public $resource = "";
  public $path = "";
  public $method = "GET";

  # construct our basecamp collection
  function __construct($data) {
    $this->accountid = $data['accountid'];
    $this->username  = $data['username'];
    $this->password  = $data['password'];
  }

  # make this class act as a Container
  public function __get($object){
    $class = "Basecamp".str_replace(" ", "", ucwords(str_replace("_", " ", $object)));
    if(isset($this->collection[$object])){
        return $this->collection[$object];
    }
    return $this->collection[$object] = new $class($this->bc_data);
  }

  # stamp current request with a resource and id, if given.
  public function work_with_resource() {
    $resources = func_get_args();
    $this->resource = array_shift($resources);
    $this->path = "";
    $this->append_path($resources);
  }
  # append a path to current request
  public function append_path($path) {
    if (empty($path)) return;
    if (is_array($path)) $path = implode("/", $path);
    $this->path = $path;
  }
  public function limit_scope($scope) {
    $this->append_path($scope);
  }

  # stamp current request with a HTTP method
  public function post_it()   { $this->method = "POST"   ;}
  public function put_it()    { $this->method = "PUT"    ;}
  public function delete_it() { $this->method = "DELETE" ;}

  # CALLS API
  public function __execute($data = array(), $options = array())
  {
    $path     = $this->resource . ( empty($this->path) ? "" : "/". $this->path) . ".json";
    $apiurl   = "https://basecamp.com/{$this->accountid}/api/{$this->version}/{$path}";
    $httphead = array('User-agent: WHMCSBasecampModule (me@nikhgupta.com)');
    
    try {
      $ch = curl_init();

      if ($this->method != "GET") {
        $httphead[] = 'Content-Type: application/json; charset=utf-8';
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
      }

      curl_setopt($ch, CURLOPT_URL, $apiurl);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $httphead);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

      $content  = curl_exec($ch);
      $response = curl_getinfo($ch);

      curl_close($ch);
    } catch (Exception $e) {
      $content = $e->getMessage();
      $response = array('http_code' => "999");
    }

    // var_dump($apiurl);
    if (in_array($response['http_code'], array(200, 201, 204))) return json_decode(trim($content));
    else throw new BasecampException(trim($content), $response['http_code']);
  }

  # create resource pseudonyms
  # projects   
  public function work_with_projects() {
    $this->work_with_resource("projects");
  }
  public function work_with_project_with_id($id) {
    if (empty($id)) throw new BasecampException("ID can not be empty!");
    $this->work_with_resource("projects", $id);
  }
  public function work_with_accesses_for_project_with_id($id) {
    if (empty($id)) throw new BasecampException("ID can not be empty!");
    $this->work_with_resource("projects", $id, "accesses");
  }

  # persons
  public function work_with_persons() {
    $this->work_with_resource("people");
  }
  public function work_with_person_with_id($id) {
    if (empty($id)) throw new BasecampException("ID can not be empty!");
    $this->work_with_resource("people", $id);
  }

  # todolists
  public function work_with_todolists($project_id) {
    if (empty($project_id)) throw new BasecampException("Project ID can not be empty!");
    $this->work_with_resource("projects", $project_id, "todolists");
  }
  public function work_with_todolist_with_id($project_id, $id) {
    if (empty($id)) throw new BasecampException("ID can not be empty!");
    if (empty($project_id)) throw new BasecampException("Project ID can not be empty!");
    $this->work_with_resource("projects", $project_id, "todolists", $id);
  }

  #todos
  public function work_with_todo_with_id($project_id, $id) {
    if (empty($id)) throw new BasecampException("ID can not be empty!");
    if (empty($project_id)) throw new BasecampException("Project ID can not be empty!");
    $this->work_with_resource("projects", $project_id, "todos", $id);
  }
  public function work_with_todos_for_list_with_id($project_id, $id) {
    if (empty($id)) throw new BasecampException("ID can not be empty!");
    if (empty($project_id)) throw new BasecampException("Project ID can not be empty!");
    $this->work_with_resource("projects", $project_id, "todolists", $id, "todos");
  }

}

/**
* Basecamp Exception Class
*/
class BasecampException extends Exception
{ 
 // Redefine the exception so message isn't optional
  public function __construct($message, $code = 0, Exception $previous = null) {
    // make sure everything is assigned properly
    parent::__construct($message, $code, $previous);
  }

  // custom string representation of object
  public function __toString() {
      return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }
}

?>
