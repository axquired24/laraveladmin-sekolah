<?php

namespace App\Admin\Controllers;

use App\Http\Collection\RouteCollection;
use function App\Http\hl_ifIsset;
use function App\Http\hl_routeCurrentAction;
use App\Models\Kelas;
use App\Models\Sekolah;
use App\Models\Siswa;
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
    	$controller = $this;
        return Admin::content(function (Content $content) use($controller) {

	        $content->header('Daftar Kelas');
	        $content->description($this->sekolah->name);

	        $content->row(function(Row $row) use($controller) {
		        $row->column(3, $controller->generateUserProfile($this->sekolah));
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
		        $row->column(3, $this->sekolah->name);
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

	        $grid->name('Kelas')->display(function($col) {
		        $url = url(RouteCollection::$siswa . "?&kelas_id=".$this->id);
		        $csiswa = $this->sekolah()->count();
		        return '<a title="'.$csiswa.' siswa" href="'.$url.'">'.$col.'</a>';
	        })->sortable();

//            $grid->name('Kelas')->sortable();
            $grid->school_year('Tahun Ajaran')->sortable();
	        $grid->siswa()->count('Jumlah Siswa')->display(function($col) {
		        return $col . ' siswa';
	        });
            //$grid->sekolah()->name('Sekolah');

            $grid->created_at()->sortable();
            // $grid->updated_at();

	        $sekolahs = Sekolah::select(['id', 'name'])->get();
	        $sekolahs = collect($sekolahs)->map(function($item) {
		        return [$item->id => $item->name];
	        })->flatten()->toArray();

	        $grid->disableExport();
	        $grid->disableFilter();
	        $grid->disableRowSelector();

	        $grid->disableCreateButton();

	        $grid->filter(function($filter) use($sekolahs) {
	        	 $filter->equal('sekolah_id')->select($sekolahs);
	        });

	        $grid->tools(function (Grid\Tools $tools) {
		        $route = url(RouteCollection::$sekolah);
		        $croute = url(RouteCollection::$kelas . '/create?sekolah_id=' . $this->sekolah->id);

		        $createbtn = <<<EOT
<a href="{$croute}" class="btn btn-sm btn-success pull-right">
    <i class="fa fa-save"></i>&nbsp;&nbsp;New
</a>
EOT;
		        $listbtn = $this->generateBackToList($route);
		        $tools->prepend($listbtn);
		        $tools->append($createbtn);
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
    	$controller = $this;

	    $kelas = Kelas::find($id);

	    $sekolah_id = optional($kelas)->sekolah_id;
	    if(hl_routeCurrentAction() == RouteCollection::CREATE) {
	    	$sekolah_id = $this->sekolah->id;
	    }

	    $currentRoute = url(RouteCollection::$kelas . '?sekolah_id=' . $sekolah_id);
	    return Admin::form(Kelas::class, function (Form $form) use($currentRoute, $controller) {

	    	$is_new = in_array(hl_routeCurrentAction(), [RouteCollection::CREATE, RouteCollection::STORE]) ? true : false;

            $form->text('name', 'Kelas');
            $form->text('school_year', 'Tahun Ajaran');
            $form->display('student_count', 'Jml Siswa')->value(0);
            // !important column

		    // Jika form = Create NEW, sisipkan default sekolah_id untuk kolom db
		    if($is_new) {
			    $form->hidden('sekolah_id', 'Sekolah')->value(optional($this->sekolah)->id);
		    }

            $form->saved(function() use($form, $is_new) {
            	if($is_new) {
		            admin_toastr('Kelas Added');
		            return redirect(url(RouteCollection::$kelas . '?sekolah_id=' . optional($form->model())->sekolah_id));
	            } else {
		            admin_toastr('update success');
		            return redirect(RouteCollection::$kelas . '/' . $form->model()->id.'/edit');
	            }
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

    function generateUserProfile(Sekolah $sekolah) {
    	$schoolphoto = "https://pbs.twimg.com/profile_images/893459442758369282/o7XXYkqN_400x400.jpg";
    	$kelas_ids  = $sekolah->kelas()->pluck('id')->toArray();
    	$siswa_count = Siswa::whereIn('kelas_id', $kelas_ids)->count();
    	return <<<EOT
<div class="box box-danger">
	<div class="box-body box-profile">
	  <img class="profile-user-img img-responsive img-circle" src="{$schoolphoto}" alt="Foto Sekolah">
	
	  <h3 class="profile-username text-center">$sekolah->name</h3>
	
	  <p class="text-muted text-center">$sekolah->address</p>
	
	  <ul class="list-group list-group-unbordered">
	    <li class="list-group-item">
	      <b>Guru BK</b> <a class="pull-right">$sekolah->bk_teacher</a>
	    </li>
	    <li class="list-group-item">
	      <b>Jumlah Kelas</b> <a class="pull-right">{$sekolah->kelas()->count()} Kelas</a>
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
