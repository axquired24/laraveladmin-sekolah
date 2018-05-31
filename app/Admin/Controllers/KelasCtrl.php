<?php

namespace App\Admin\Controllers;

use App\Http\Collection\RouteCollection;
use function App\Http\hl_ifIsset;
use App\Models\Kelas;
use App\Models\Sekolah;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Layout\Row;

class KelasCtrl extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public $sekolah;

    function __construct() {
	    $sekolah_id = hl_ifIsset("sekolah_id");
	    $this->sekolah = $sekolah_id != null ? Sekolah::find($sekolah_id) : null;
    }

    public function index()
    {
        return Admin::content(function (Content $content) {

	        $content->header('Daftar Kelas');
	        $content->description($this->sekolah->name);

	        $content->row(function(Row $row) {
		        $row->column(3, $this->sekolah->name);
		        $row->column(9, $this->grid());
	        });

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

            $content->header('Edit Kelas');
            $content->description('');

            $content->body($this->form($id)->edit($id));
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

            $content->header('Tambah Kelas');
            $content->description('');

	        $content->row(function(Row $row) {
		        $row->column(3, 'foo');
		        $row->column(9, $this->form());
	        });
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Kelas::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->name('Kelas')->sortable();
            $grid->sekolah()->name('Sekolah');

            $grid->created_at()->sortable();;
            // $grid->updated_at();
	        $grid->disableExport();

	        $sekolahs = Sekolah::select(['id', 'name'])->get();
	        $sekolahs = collect($sekolahs)->map(function($item) {
		        return [$item->id => $item->name];
	        })->flatten()->toArray();

	        $grid->disableFilter();
	        $grid->disableRowSelector();

	        $grid->filter(function($filter) use($sekolahs) {
	        	 $filter->equal('sekolah_id')->select($sekolahs);
	        });

	        $grid->tools(function (Grid\Tools $tools) {
		        $route = RouteCollection::$sekolah;
		        $listbtn = <<<EOT
<a href="{$route}" class="btn btn-sm btn-default">
	<i class="fa fa-chevron-left"></i> Kembali
</a> &nbsp;
EOT;
		        $tools->append($listbtn);
	        });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($id = null)
    {
	    $kelas = Kelas::find($id);

	    $currentRoute = url(RouteCollection::$kelas . '?sekolah_id=' . optional($kelas)->sekolah_id);
	    return Admin::form(Kelas::class, function (Form $form) use($currentRoute) {

            $form->text('name', 'Kelas');

            $form->saved(function($saved) use($form) {
	            admin_toastr('update success');
	            return redirect(RouteCollection::$kelas . '/' . $form->model()->id.'/edit');
            });

            $form->tools(function (Form\Tools $tools) use($currentRoute) {
            	$tools->disableListButton();
            	$tools->disableBackButton();

            	$listbtn = <<<EOT
<a href="{$currentRoute}" class="btn btn-sm btn-default">
	<i class="fa fa-chevron-left"></i> Back to List
</a> &nbsp;
EOT;
            	$tools->add($listbtn);
            });
        });
    }
}
