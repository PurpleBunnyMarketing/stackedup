<?php

namespace App\Http\ViewComposers;

use App\Models\Package;
use App\Models\Setting;
use Illuminate\View\View;

/**
 * To Manage View Details
 */
class LoginComposer
{
	public $setting = '';
	public $monthlyPackage = '';
	public $annualPackage = '';


	public function __construct()
	{
		$this->setting = Setting::pluck('value', 'constant');
		$this->monthlyPackage = Package::select('amount')->where('package_type', 'monthly')->first()->amount;
		$this->annualPackage = Package::select('yearly_off_amount')->where('package_type', 'yearly')->first()->yearly_off_amount;
	}

	public function compose(View $view)
	{
		$data = [
			'sitesetting' => $this->setting,
			'package' => [
				'monthly_price' => $this->monthlyPackage,
				'yearly_discount' => $this->annualPackage,
			],
			'mend_sign' => '<span class="mendatory">*</span>',
		];
		$view->with($data);
	}
}
