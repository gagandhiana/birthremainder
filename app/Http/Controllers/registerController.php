<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Models\User;

class registerController extends Controller
{
    public function showr(){
        return view('register');
    }
    public function showl(){
        return view('login');
    }

    public function signup(Request $request){
        $request->validate([
            'name'=>'required',
            'birthdate'=>'required',
            'email'=>'required',
            'password'=>'min:6',
            'confirmpassword'=>'required_with:password|same:password'
        ]);
        $user=new User;
        $user->name=$request->get('name');
        $user->birthdate=$request->get('birthdate');
        $user->email=$request->get('email');
        $user->password=$request->get('password');
        $user->save();
        return redirect('/');
    }
    public function signin(Request $request){
        $email=$request->get('email');
        $password=$request->get('password');
        $userdata=User::where(['email'=>$email,'password'=>$password])->first();
        if(!empty($userdata)){
            Session::put("id_session",$userdata->id);
            Session::put("name_session",$userdata->name);
            return redirect('/');
        }else{
            return back()->with('message','Email Or Password Wrong');
        }
    }
    public function signout(){
        session::flush();
        return redirect('r');
    }
}
