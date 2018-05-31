<?php

namespace App\Admin\Controllers;

use App\Models\Sekolah;
use App\Http\Collection\RouteCollection;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class SekolahCtrl extends Controller
{
    use ModelForm;

	/**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('Daftar Sekolah');
            $content->description('');

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
        return Admin::grid(Sekolah::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->name('Sekolah')->display(function($col) {
	            $url = url(RouteCollection::$kelas . "?&sekolah_id=".$this->id);
	            $ckelas = $this->kelas()->count();
                return '<a title="'.$ckelas.' kelas" href="'.$url.'">'.$col.'</a>';
            })->sortable();

            $grid->bk_teacher('Guru BK')->sortable();
            $grid->address('Alamat')->sortable();

            $grid->created_at();
            // $grid->updated_at();

	        // The filter($callback) method is used to set up a simple search box for the table
	        $grid->disableFilter();
	        $grid->disableRowSelector();

	        // Set Pagination
	        // $grid->paginate(5);
	        $grid->disableExport();

//	        $grid->actions(function($action) {
//	        	$c_kelas = $action->row->kelas()->count();
//		        $url = url(RouteCollection::$kelas . "?&sekolah_id=".$action->getKey());
//		        $url = '<a class="btn btn-xs btn-default" href="'.$url.'">'.$c_kelas.' Kelas</a> &nbsp;';
//		        $action->prepend($url);
//	        });
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
