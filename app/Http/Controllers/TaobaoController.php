<?php

namespace App\Http\Controllers;

use App\Helpers\TaobaoHelper;
use App\Services\TaobaoService;
use Illuminate\Http\Request;


/**
 * 淘宝相关
 * Class TaobaoController
 * @package App\Http\Controllers
 */
class TaobaoController extends Controller
{
    /**
     * 重定向到淘宝授权登录地址
     */
    public function auth(){
        return redirect('https://oauth.taobao.com/authorize?response_type=token&client_id=23225630&state=pyt&view=wap');
    }

    /**
     * 保存淘宝授权信息
     * @param Request $request
     */
    public function saveAuthToken(Request $request){
        $tokens = $request->post('tokens');
        $userId = $request->user()->id;

        $tokens = json_decode($tokens, true);
        if(!$tokens){
            return $this->ajaxError("参数错误");
        }
        if(!(array_key_exists('access_token', $tokens)
        && array_key_exists('token_type', $tokens)
        && array_key_exists('expires_in', $tokens)
        && array_key_exists('refresh_token', $tokens)
        && array_key_exists('re_expires_in', $tokens)
        && array_key_exists('taobao_user_id', $tokens)
        && array_key_exists('taobao_user_nick', $tokens)
        )){
            return $this->ajaxError("参数错误");
        }

        if(!(new TaobaoService())->saveAuthToken($userId, $tokens)){
            return $this->ajaxError("绑定淘宝账号失败");
        }

        return $this->ajaxSuccess();
    }

    /**
     * 保存pid
     * @param Request $request
     * @return static
     */
    public function savePid(Request $request){
        $pid = $request->post('pid');
        $userId = $request->user()->id;
        if(!$pid){
            return $this->ajaxError("参数错误");
        }
        if(!(new TaobaoHelper())->isPid($pid)){
            return $this->ajaxError("PID格式错误");
        }

        if(!(new TaobaoService())->savePid($userId, $pid)){
            return $this->ajaxError("绑定PID失败");
        }

        return $this->ajaxSuccess();
    }

    /**
     * 查询淘宝授权状态
     * @param Request $request
     */
    public function authInfo(Request $request){
        $data = (new TaobaoService())->authInfo($request->user()->id);
        return $this->ajaxSuccess($data);
    }

}
