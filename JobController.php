<?php

class JobController extends BaseController {

    /*** List Jobs **/
	public function listJobs() {
		
		$print_url = '';
		
		if(Input::has('dfrom')) {
			
			$jobs = Job::where('status', '=', 1)->
				where('job_date', '>=', Input::get('dfrom'))->
				where('job_date', '<=', Input::get('dto'))->get();
			$print_url .= '?dfrom=' . Input::get('dfrom') . 
				'&dto=' . Input::get('dto');
			
		} else {
			$jobs = Job::where('status', '=', 1)->get();
		}
		
		if($print_url != '') {
			$print_url .= '&print=1';
		} else {
			$print_url .= '?print=1';
		}
		
		if(Input::get('print')) {
			return View::make('print.job_list', array('jobs' => $jobs, 
				'print_url' => $print_url));
		} else {
			return View::make('job_list', array('jobs' => $jobs, 
				'print_url' => $print_url));
		}
	}

    /*** Create Job **/
	public function createJob() {
		if(!Request::isMethod('post')) {
			return View::make('job_form');
		} else {
			$job = new Job;
            $job->job_status = Input::get('job_status');
			$job->job_date = Input::get('date');
			$job->job_number = Input::get('job_number');
			$job->customer_reference = Input::get('customer_reference');
			$job->order_details = Input::get('order_details');
			$job->boards = Input::get('boards');
			$job->accessories = Input::get('accessories');
			$job->boards_ordered = Input::get('boards_ordered');
			$job->sales_order_complete = (Input::get('sales_order_complete') == 'no') ? 0 : 1;
			$job->notes = Input::get('notes');
			$job->save();
			
			return Redirect::to('/')->with('message', 'Your job has been created.');
			
		}
		
	}
	
    /*** Update Job **/
	public function updateJob($job_id) {
		
		$job = Job::find($job_id);
		
		if(!Request::isMethod('post')) {
			return View::make('job_form', array('job' => $job));
		} else {
            $job->job_start_time = Input::get('job_start_time');
            $job->job_end_time = Input::get('job_end_time');
			$job->job_number = Input::get('job_number');
			$job->job_date = Input::get('date');
			$job->customer_reference = Input::get('customer_reference');
			$job->order_details = Input::get('order_details');
			$job->boards = Input::get('boards');
			$job->accessories = Input::get('accessories');
			$job->boards_ordered = Input::get('boards_ordered');
			$job->sales_order_complete = (Input::get('sales_order_complete') == 'no') ? 0 : 1;
			$job->notes = Input::get('notes');
			$job->save();
			
			return Redirect::to('/')->
				with('message', 'Your job has been updated.');
		}
	}
	
    /*** Update Job ***/
	public function archiveJob($job_id) {
		
		$job = Job::find($job_id);
		$job->status = 0;
		$job->save();
		
		return Redirect::to('/')->
			with('message', 'Your job has been archived.');
	}

    /*** Restore Job ***/
	public function restoreJob($job_id) {
		
		$job = Job::find($job_id);
		$job->status = 1;
		$job->save();
		
		return Redirect::to('/')->
			with('message', 'Your job has been restored.');
	}
	
    /*** Review Archive ***/
	public function viewArchive($year, $month) {
		
		$archive_links = array();
		$archive_increment = strtotime('2014/08/01');
		$archive_end = strtotime('now');
		
		do {
			
			$archive_links[] = array('label' => date('m') . '-' . date('Y'), 
                'link' => '/job/archive/view/' . date('Y', $archive_increment) . 
                '/' . date('m', $archive_increment));
			$archive_increment = strtotime(date('Y/m/d', $archive_increment) . ' + 1 month');
			
		} while($archive_increment <= $archive_end);
		
		$archive_start_filter = $year . '/' . $month . '/01 00:00:01';
		$archive_end_filter = date('Y/m/d H:i:s', strtotime($archive_start_filter . ' + 1 month'));
		
		return View::make('job_archive', 
                array('for' => $year . '-' . $month, 'jobs' => 
                    Job::where('status', '=', '0')->
                        where('created_at', '>=', $archive_start_filter)->
                        where('created_at', '<=', $archive_end_filter)->
                        get(), 'archive_links' => $archive_links));
	}
	
    /*** Delete Job ***/
	public function deleteJob($job_id) {
		
		$job = Job::find($job_id);
		$job->delete();
		
		return Redirect::to('/')->
			with('message', 'Your job has been deleted.');
	}
}
