<?php

namespace App\Admin\Controllers;

use App\Http\Collection\RouteCollection;
use function App\Http\hl_ifIsset;
use App\Models\Hasil;
use App\Models\Kelas;
use App\Models\Siswa;

use App\Models\Tema;
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

	    $siswa_ids = Siswa::where('kelas_id', $this->kelas->id)->pluck('id')->toArray();
	    $hasils = Hasil::whereIn('siswa_id', $siswa_ids)->pluck('answer')->map(function($item) {
	    	return json_decode($item);
	    })->collapse();

	    $uniq_hasil = $hasils->unique()->map(function ($item) use($hasils) {
	    	$counthasil = $hasils->filter(function($hasil) use($item) {
	    		return $hasil == $item;
		    })->count();

	    	return (object) [
	    		'tema' => Tema::find($item),
			    'counter' => $counthasil
		    ];
	    })->sortByDesc('counter')->toArray();

	    $timeline_item = "";
	    foreach ($uniq_hasil as $item) {
	    	$counter = '<strong>('.$item->counter.' Siswa Memilih Tema)</strong>';
	    	//. $item->tema->description;
		    $timeline_item .= $controller->genTimelineItem('', $item->tema->name, $counter, 'bg-red');
	    }

	    $timeline = <<<EOT
<br>
<ul class="timeline">
	<li class="time-label">
          <span class="bg-red">
            Tema Terpopuler
          </span>
    </li>
	$timeline_item
</ul>
EOT;
        return Admin::content(function (Content $content) use($controller, $timeline) {

            $content->header('Daftar Siswa');
            $content->description('Kelas ' . $controller->kelas->name . ' | ' . $controller->kelas->sekolah->name);

	        $content->row(function(Row $row) use($controller, $timeline) {
		        $row->column(3, $controller->generateUserProfile($controller->kelas) . $timeline);
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

	        $grid->actions(function($action) {
	        	$route = url(RouteCollection::$siswa . '/' . $action->getKey() . '/hasil');
		        $action->prepend('<a href="'.$route.'" title="Lihat Hasil Survei"><i class="fa fa-file-word-o"></i></a>&nbsp;');
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

    public function hasil($siswa_id) {
    	$controller = $this;
    	$siswa = Siswa::findOrFail($siswa_id);
	    return Admin::content(function (Content $content) use($controller, $siswa) {

		    $content->header('Detail Siswa');
		    $content->description($siswa->name);

		    $content->row(function(Row $row) use($controller, $siswa) {
		    	$answer = json_decode($siswa->hasil->answer);
			    $temas = Tema::whereIn('id', $answer)->get();

			    $timeline_item = "";

			    foreach ($temas as $index => $tema) {
			    	$timeline_item .= $controller->genTimelineItem($index+1, $tema->name, $tema->description);
			    }

			    $timeline = <<<EOT
<ul class="timeline">
	<li class="time-label">
          <span class="bg-red">
            Tema yang dipilih
          </span>
    </li>
	$timeline_item
</ul>
EOT;
			    $backroute = url(RouteCollection::$siswa . '?kelas_id=' . $siswa->kelas_id);
			    $row->column(3, $controller->generateBackToList($backroute) . '<br><br>' . $controller->genSiswaDetail($siswa));
			    $row->column(9, $timeline);
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

	function genSiswaDetail(Siswa $siswa) {
		$schoolphoto = "https://pbs.twimg.com/profile_images/893459442758369282/o7XXYkqN_400x400.jpg";
		$gender = $siswa->gender == "m" ? "Male" : "Female";
		return <<<EOT
<div class="box box-danger">
	<div class="box-body box-profile">
	  <img class="profile-user-img img-responsive img-circle" src="{$schoolphoto}" alt="Foto Sekolah">
	
	  <h3 class="profile-username text-center">$siswa->name
	  <small><br> Kelas {$siswa->kelas->name}</small>
	  </h3>
	
	  <p class="text-muted text-center">{$siswa->kelas->sekolah->name}</p>
	
	  <ul class="list-group list-group-unbordered">
	    <li class="list-group-item">
	      <b>Guru BK</b> <a class="pull-right">{$siswa->kelas->sekolah->bk_teacher}</a>
	    </li>
	    <li class="list-group-item">
	      <b>NIK</b> <a class="pull-right">$siswa->nik</a>
	    </li>
	    <li class="list-group-item">
	      <b>Gender</b> <a class="pull-right">$gender</a>
	    </li>
	  </ul>
	
	</div>
	<!-- /.box-body -->
</div>
EOT;
	}

	function genTimelineItem($index, $title, $content, $iconbg='bg-blue', $icon='fa-chevron-right') {
    	return <<<EOT
<li>
  <i class="fa {$icon} {$iconbg}"></i>

  <div class="timeline-item">
    <span class="time"><i class="fa fa-clock-o"></i> </span>

    <h3 class="timeline-header"><a href="#">#{$index} {$title}</a></h3>

    <div class="timeline-body">
      {$content}
    </div>
  </div>
</li>
EOT;

	}
}
