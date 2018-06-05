<?php

namespace App\Admin\Controllers;

use App\Http\Collection\RouteCollection;
use function App\Http\hl_ifIsset;
use App\Models\Kelas;
use App\Models\Siswa;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Layout\Row;

class SiswaCtrl extends Controller
{
    use ModelForm;

    public $kelas;

	function __construct() {
		$kelas_id = hl_ifIsset("kelas_id");
		$this->kelas = $kelas_id != null ? Kelas::find($kelas_id) : null;
	}

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
    	$controller = $this;
        return Admin::content(function (Content $content) use($controller) {

            $content->header('Daftar Siswa');
            $content->description('Kelas ' . $controller->kelas->name . ' | ' . $controller->kelas->sekolah->name);

	        $content->row(function(Row $row) use($controller) {
		        $row->column(3, $controller->generateUserProfile($controller->kelas));
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

            $content->header('Edit Data Siswa');
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
        return Admin::grid(Siswa::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->nik('NIK')->sortable();
            $grid->name('Nama')->sortable();
            $grid->kelas()->name('Kelas');
            $grid->gender('Gender')->display(function ($gender) {
            	return $gender == "m" ? "Male" : "Female";
            });
            $grid->created_at();

	        $grid->disableExport();
	        $grid->disableFilter();
	        $grid->disableRowSelector();

	        $grid->disableCreateButton();

	        $kelases = Kelas::select(['id', 'name'])->get();
	        $kelases = collect($kelases)->map(function($item) {
	        	return [$item->id => $item->name];
	        })->flatten()->toArray();

	        $grid->filter(function($filter) use($kelases) {
		        $filter->equal('kelas_id')->select($kelases);
	        });

	        $grid->tools(function (Grid\Tools $tools) {
		        $route = url(RouteCollection::$kelas . '?sekolah_id=' . $this->kelas->sekolah_id);

		        $listbtn = $this->generateBackToList($route);
		        $tools->prepend($listbtn);
	        });

//            $grid->updated_at();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($id = null)
    {
    	$controller = $this;
    	$siswa = Siswa::find($id);
    	$kelas_id = optional($siswa)->kelas_id;
	    $currentRoute = url(RouteCollection::$siswa . '?kelas_id=' . $kelas_id);

	    return Admin::form(Siswa::class, function (Form $form) use ($currentRoute, $controller) {
	        $form->text('nik', 'NIK');
	        $form->text('name', 'Nama Lengkap');
	        $form->select('gender', 'Gender')->options([
	        	'm' => 'Male',
		        'f' => 'Female'
	        ]);

            // $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Update Terakhir');

		    $form->saved(function() use($form) {
			    admin_toastr('update success');
			    return redirect(RouteCollection::$siswa . '/' . $form->model()->id.'/edit');
		    });

		    $form->tools(function (Form\Tools $tools) use($currentRoute, $controller) {
			    $tools->disableListButton();
			    $tools->disableBackButton();

			    $listbtn = $controller->generateBackToList($currentRoute);
			    $tools->add($listbtn);
		    });
        });
    }

	function generateBackToList($url = null) {
		return <<<EOT
<a href="{$url}" class="btn btn-sm btn-default">
<i class="fa fa-chevron-left"></i> Kembali</a> &nbsp;
EOT;
	}

	function generateUserProfile(Kelas $kelas) {
		$schoolphoto = "https://pbs.twimg.com/profile_images/893459442758369282/o7XXYkqN_400x400.jpg";
		$siswa_count = $kelas->siswa->count();
		return <<<EOT
<div class="box box-danger">
	<div class="box-body box-profile">
	  <img class="profile-user-img img-responsive img-circle" src="{$schoolphoto}" alt="Foto Sekolah">
	
	  <h3 class="profile-username text-center">Kelas $kelas->name 
	  <small><br> {$kelas->sekolah->name}</small>
	  </h3>
	
	  <p class="text-muted text-center">{$kelas->sekolah->address}</p>
	
	  <ul class="list-group list-group-unbordered">
	    <li class="list-group-item">
	      <b>Guru BK</b> <a class="pull-right">{$kelas->sekolah->bk_teacher}</a>
	    </li>
	    <li class="list-group-item">
	      <b>Jumlah Siswa</b> <a class="pull-right">$siswa_count Siswa</a>
	    </li>
	  </ul>
	
	  <a href="#" class="btn btn-default btn-block"><b>Lihat Detail</b></a>
	</div>
	<!-- /.box-body -->
</div>
EOT;

	}
}
