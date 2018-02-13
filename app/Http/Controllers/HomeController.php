<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Contract;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $users = Contract::paginate(5);
        
        return view('home')->with('tasks',$users);
    }

    public function search(Request $request)
    {
        if($request->ajax()){
            $output="";
            $search=DB::table('contracts')->where('f_name','LIKE','%'.$request->search.'%')
                ->orWhere('n_name','LIKE','%'.$request->search.'%')
                ->where('c_email','LIKE','%'.$request->search.'%')->get();
            if($search){
                foreach ($search as $key => $sa) {
                    $output.='<tr>'.
                            '<td>'. $sa->pro_pic. '</td>'.
                            '<td>'. $sa->f_name. '</td>'.
                            '<td>'. $sa->n_name. '</td>'.
                            '<td>'. $sa->c_email. '</td>'.
                            '<td>'. $sa->cont_1. '</td>'.
                            '<td>'. $sa->pro_pic. '</td>'.
                          '</tr>';
                }
                
                return Response($output);
            }
        }   
    }


    public function create()
    {
                
        return view('actions.person');
    }


    public function add(Request $request)
    {
        $input=$request->except('pro_pic');

        $this->validate($request,[
        'f_name' => 'required|string|max:30',
        'c_email' => 'required|string|email|max:30',
        'cont_1' => 'required|max:11|min:11',
        'pro_pic'=>'image|mimes:png,jpg,jpeg|max:10000'
        ]);

        //image upload
        $pro_pic=$request->pro_pic;
        if($pro_pic){
            $imageName=$pro_pic->getClientOriginalName();
            $pro_pic->move('images',$imageName);
            $input['pro_pic']=$imageName;
        }

        Contract::create($input);
        
        return redirect('/');
    }


    public function view($id)
    {
        
        $tasks = Contract::findOrFail($id);;

            return view('actions.view_person')->with('tasks',$tasks);
    }


    public function delete($id)
    {
        $task = Contract::findOrFail($id);

        $task->delete();
        return redirect('/');
    }

    public function edit($id)
    {
        $task = Contract::findOrFail($id);

        return view('actions.update')->with('tasks',$task);
    }

    public function update($id, Request $request)
    {
        $task = Contract::findOrFail($id);

        $this->validate($request,[
        'f_name' => 'required|string|max:30',
        'c_email' => 'required|string|email|max:30',
        'cont_1' => 'required|max:11|min:11',
        'pro_pic'=>'image|mimes:png,jpg,jpeg|max:10000'
        ]);

        $input = $request->all();

        $task->fill($input)->save();         

        return redirect('/');
    }


}
