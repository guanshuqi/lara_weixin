<?php

namespace App\Admin\Controllers;

use App\Model\WeixinUserModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

use GuzzleHttp;


class WxuserController extends Controller
{
    use HasResourceActions;

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

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WeixinUserModel);



        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(WeixinUserModel::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WeixinUserModel);



        return $form;
    }

    /**
     * 群发视图
     * @param Content $content
     * @return Content
     */
    public function sendMsgView(Content $content){
        return $content
            ->header('Create')
            ->description('description')
            ->body(view('admin.weixin.user'));
    }
    /**
     * 群发消息
     */
    public function sendMsg(){
        $userInfo=WeixinUserModel::all()->toArray();
        $openid=array_column($userInfo,'openid');
        //echo '<pre>';print_r($openid);echo '</pre>';die;
        //获取access_token
        $access_token=WeixinUserModel::getAccessToken();
        //调用接口
        $url='https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$access_token.'';
        $client = new GuzzleHttp\Client(['base_url' => $url]);
        $reponse=[
            'touser'=>$openid,
            'msgtype'=>'text',
            'text'=>[
                'content'=>$_POST['msg']
            ]
        ];
        //print_r($reponse);die;
        $r = $client->Request('POST', $url, [
            'body' => json_encode($reponse, JSON_UNESCAPED_UNICODE)
        ]);
        $response_arr = json_decode($r->getBody(), true);
        //print_R($response_arr);die;
        if ($response_arr['errcode'] == 0) {
            echo "发送成功";
        } else {
            echo "发送失败";
            echo '</br>';
            echo $response_arr['errmsg'];

        }
    }
}
