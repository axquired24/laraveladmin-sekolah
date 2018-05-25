<?php

namespace App\Admin\Controllers;

use App\Models\Sekolah;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class SekolahCtrl extends Controller
{
    use ModelForm;

    protected $routes;

    function __construct() {
    	$this->routes = 'admin/sekolah';
    }

	/**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
	    $routes = $this->routes;
        return Admin::grid(Sekolah::class, function (Grid $grid) use($routes) {

            $grid->id('ID')->sortable();

            $grid->name('Sekolah')->display(function($col) use($routes) {
            	$url = url($routes . "/$this->id/edit");
                return '<a href="'.$url.'">'.$col.'</a>';
            })->sortable();

            $grid->bk_teacher('Guru BK')->sortable();
            $grid->address('Alamat')->sortable();

            $grid->created_at();
            // $grid->updated_at();

	        // The filter($callback) method is used to set up a simple search box for the table
	        $grid->filter(function ($filter) {

		        // Sets the range query for the created_at field
		        $filter->between('created_at', 'Created Time')->datetime();
	        });

	        // Set Pagination
	        // $grid->paginate(5);
	        $grid->disableExport();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Sekolah::class, function (Form $form) {

            // $form->display('id', 'ID');
	        $form->display('name','Sekolah');
	        $form->display('bk_teacher','Guru BK');
	        $form->display('address','Alamat');

            // $form->display('created_at', 'Created At');
             $form->display('updated_at', 'Updated At');
        });
    }
}
