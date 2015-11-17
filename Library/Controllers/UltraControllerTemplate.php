
namespace {{namespace}};

class {{class_name}} extends \UltraController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->Load->view('{{class_name}}/index');
	}

}
