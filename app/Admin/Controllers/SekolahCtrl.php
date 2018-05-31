<?php

namespace App\Admin\Controllers;

use App\Models\Sekolah;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Http\Collection\RouteCollection;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Layout\Row;

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
    	$controller = $this;
        return Admin::content(function (Content $content) use($controller) {

            $content->header('Daftar Sekolah');
            $content->description('');

	        $content->row(function(Row $row) use($controller) {
	        	$row->column(4, $controller->generateInfoBox('building-o', 'Sekolah Terdaftar', Sekolah::count(), 'red'));
	        	$row->column(4, $controller->generateInfoBox('slack', 'Kelas Terdaftar', Kelas::count(), 'orange'));
	        	$row->column(4, $controller->generateInfoBox('user', 'Siswa Terdaftar', Siswa::count(), 'green'));
	        });

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
	        $form->text('name','Sekolah');
	        $form->text('bk_teacher','Guru BK');
	        $form->textarea('address','Alamat');

            // $form->display('created_at', 'Created At');
             $form->display('updated_at', 'Updated At');
        });
    }

    protected function generateInfoBox($icon, $label, $value, $color) {
    	return <<<EOT
<div class="info-box">
	<span class="info-box-icon bg-{$color}"><i class="fa fa-{$icon}"></i></span>
	<div class="info-box-content">
	  <span class="info-box-text">$label</span>
	  <span class="info-box-number">$value</span>
	</div>
	<!-- /.info-box-content -->
</div>
EOT;

    }
}
