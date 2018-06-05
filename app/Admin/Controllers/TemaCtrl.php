<?php

namespace App\Admin\Controllers;

use App\Models\Tema;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class TemaCtrl extends Controller
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

            $content->header('Daftar Tema');
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

            $content->header('Edit Tema');
            $content->description('');

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

            $content->header('Tambah Tema');
            $content->description('');

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
        return Admin::grid(Tema::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->name('Tema')->sortable();

            $grid->created_at();
            //$grid->updated_at();

            $grid->disableRowSelector();
            $grid->disableExport();
            $grid->disableFilter();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Tema::class, function (Form $form) {

            //$form->display('id', 'ID');
	        $form->text('name', 'Tema');
	        $form->textarea('description', 'Deskripsi');

            // $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
