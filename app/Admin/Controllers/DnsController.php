<?php

namespace App\Admin\Controllers;

use App\Models\Dns;
use App\Models\SpiderMovie;
use App\Services\AliYun\DnsService;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Symfony\Component\HttpFoundation\Response;

class DnsController extends AdminController
{
    use Form\HasHooks;
    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Dns());
        $grid->column('Type', '类型');
        $grid->column('RR', '主机记录');
        $grid->column('Value', '记录值');
        $grid->column('Status', '状态')->switch([
            'on' => ['value' => 'ENABLE', 'text' => '启用', 'color' => 'primary'],
            'off' => ['value' => 'Disable', 'text' => '禁用', 'color' => 'default'],
        ]);
        $grid->disableFilter();
        return $grid;
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $dns = new Dns();
        $show = new Show($dns->findOrFail($id));
        $show->field('Type', '类型');
        $show->field('RR', '主机记录');
        $show->field('Value', '记录值');
        $show->field('Status', '状态')->using(['ENABLE' => '启用', 'DISABLE' => '禁用']);;

        return $show;
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Dns());
        $form->select('Type', '类型')->options(DnsService::$typeMap)->rules('required');
        $form->text('RR', '主机记录')->rules('required');
        $form->text('Value', '记录值')->rules('required');
        $form->footer(function ($footer) {
            // 去掉`重置`按钮
            $footer->disableReset();

            // 去掉`查看`checkbox
            $footer->disableViewCheck();

            // 去掉`继续编辑`checkbox
            $footer->disableEditingCheck();
        });

        return $form;
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

}
