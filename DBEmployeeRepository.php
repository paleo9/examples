<?php namespace Chi\repos;

// paths removed in examples
use DBRepository;
use EmployeeRepository;

class DBEmployeeRepository extends DBRepository implements EmployeeRepository {

	private $rules;

	public function __construct(\Employee $model) {
		$this->model = $model;
	}

	private function setValidationRules($id) {
	// Why all the extra parameters in the employers_id field?
	// Because we have to tell it to ignore the current record when checking for uniquness!
	// See http://laravel.com/docs/4.2/validation#rule-unique for even more on unique.
		$this->rules = [
		'forename' 			=> 'required',
		'surname' 			=> 'required',
		'employers_id'		=> 'required', // |unique:employees,employers_id,'.$id,
		'date_of_birth' 	=> 'date',
		'email_address' 	=> 'email',
		'height' 			=> 'integer',
		'weight' 			=> 'integer',
		'date_left' 		=> 'date'
		];
	}

	/**
	* Sets the current employee.
	*/
	public function setCurrentEmployee($id) {
		$emp = $this->model->where('uid', $id)->first();
		\Session::put('currentEmployee', $emp->id);
		\RecentEmployee::where('employee_id', $emp->id)->delete();
		\RecentEmployee::create([
		'employee_id'	=> $emp->id,
		'user_id'		=> \Auth::id()
		]);
	}

	/**
	* Create a new employee...
	* $values usually contains Input::all();
	*/
	public function createEmployee($values) {
		$this->setValidationRules(0);                       // Set the validation ID so the validator knows which record to ignore when setting unique values.
		$val = \Validator::make($values, $this->rules);		// Validate the new data.

		if($val->fails()){									// If the validation fails, return the validator for error messages.
			return $val;
		}

		// $num = $this->updateContracts($emp);
		$this->model->create($values);						// Update the record.
		return null;										// All is good!
	}

	/**
	* Updates an employee by UID...
	* $values would contain Input::all();
	* n.b. UID used for historical reasons
	*/
	public function updateEmployeeByUid($uid, $values) {
		$emp = $this->getByUID($uid);						// Get the record to be updated.
		$this->setValidationRules($emp->id);				// Set the validation ID so the validator knows which record to ignore when setting unique values.
		$val = \Validator::make($values, $this->rules);		// Validate the new data.

		if($val->fails()){									// If the validation fails, return the validator for error messages.
			return $val;
		}

		// $num = $this->updateContracts($emp);
		$emp->update($values);								// Update the record.
		return null;										// All is good!
	}


	/**
	* Get an ordered, 'LIKE %val%' list of employees.
	*/
	public function getOrderedListLike($like, $limit = 50) {

		/*  equivalent SQL:
		whereRaw(
		"SELECT * from employees
		WHERE forename like 'word'
		union all
		SELECT * from vocabulary
		WHERE translation LIKE '%word%' and translation not like 'word'",
		array($q)
		)
		*/

		$pos = 0;
		$not = \DB::table('employees'); //new \Employee();

		$searchTerms = explode(' ', \Input::get('namesearch'));

		// $this->model->hasSelectedContract();

		foreach($searchTerms as $term) {
			if($pos < 1) {
				$pos++;
				$this->model = $this->model->where('employers_id', 'like', $term);
			} else {
				$this->model = $this->model->orWhere('employers_id', 'like', $term);
			}

			$this->model = $this->model->orWhere('forename', 'like', $term);
			$this->model = $this->model->orWhere('surname', 'like', $term);
		}

		foreach($searchTerms as $term) {
			if($pos < 1) {
				$pos++;
				$not = $not->where(function($q) use($term) {
					$q->where('employers_id', 'like', '%'.$term.'%');
					$q->where('employers_id', 'not like', $term);
				});
			} else {
				$not = $not->orWhere(function($q) use($term) {
					$q->where('employers_id', 'like', '%'.$term.'%');
					$q->where('employers_id', 'not like', $term);
				});
			}

			$not = $not->orWhere(function($q) use($term) {
				$q->where('surname', 'like', '%'.$term.'%');
				$q->where('surname', 'not like', $term);
			});

			$not = $not->orWhere(function($q) use($term) {
				$q->where('forename', 'like', '%'.$term.'%');
				$q->where('forename', 'not like', $term);
			});
		}

		return $this->model->unionAll($not)->take($limit);
		return $this->model->orderBy('forename')->orderBy('surname')->unionAll($not)->take(150000); //$limit);
	}


	public function getAdvOrderedListLike($details, $limit = 50) {
		Log::info('In getAdvOrderedListLike');
		$terms = [
			'location' => '%' . $details['location'] . '%',
			'forename' => '%' . $details['forename'] . '%',
			'surname' => '%' . $details['surname'] . '%',
			'address' => '%' . $details ['address'] . '%',
			//'address' => $details ['address'],
			'town' => $details ['address'],
			'postcode' => '%' . $details['postcode']  . '%',
			'ni' => '%' .  $details['ni'] . '%',
			'nhs' => '%' .  $details['nhs'] . '%'
		];


		$employees = \Employee::where('surname', 'like', $terms['surname'])
			->where('address1', 'like', $terms['address'])
			->orWhere('address2', 'like', $terms['address'])
			->orWhere('address3', 'like', $terms['address'])
			->orWhere('address4', 'like', $terms['address'])
			->Limit(50)->get();

		$results = $employees;
		return  $results;
	}

	public function getEmployeesByContractUid($contract_uid) {
		return Contract::where('uid', $contract_uid)
		->with('employees');
	}

	/**
	* Lists all employees in a contract.
	* Param: id of a contract.
	*/
	public function getEmployeesByContractId($contract_id) {
		return Contract::where('id', $contract_id)->with('employees');
	}

	public function getMostRecentWorkRole($employee_id){
		return WorkRole::find($employee_id)
				->where('date_to', null)
				->orderBy('date_from', 'DESC')
				->first();
	}

}
