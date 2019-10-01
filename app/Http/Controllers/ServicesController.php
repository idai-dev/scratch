<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services;
use App\Mots;
use Auth;

class ServicesController extends Controller
{
    function index(){
        $services = Services::OrderBy('created_at','DESC')->paginate(4);
        return view('layouts/index')->with('service',$services);
    }
    function viewrecherche(){
        return view('layouts/rechercheview');
    }

    function afficher(Request $request){
        return $request;
    }
    function AjouterServicesForm(){
        return view('layouts/AjouterServicesForm');
    }

    function AjouterServicesAction(Request $request){
        $document="";
       if ($request->hasFile('document')) {
           $document= rand(1, 10000).'_'.$request->file('document')->getClientOriginalName();
           $request->file('document')->move("images\Document", $document);
       }
        $tab = count($request->tags);
        $service = new Services;
        $service->id_user = Auth::user()->id;
        $service->titre = $request->titre;
        $service->type = $request->type;
        $service->localisation = $request->localisation;
        $service->salaire_min = $request->salaire_min;
        $service->salaire_max = $request->salaire_max;
        $service->description = $request->description;
        $service->document = $document;
        $service->save();
        $identifient = $service->id;
        for ($k=0; $k<$tab;$k++){
            $mot = new mots;
            $mot->id_offre = $identifient;
            $mot->tags = $request->tags[$k];
            $mot->id_user = Auth::user()->id;
            $mot->save();
        }
        
        return back()->with('status','Ajout términé avec succées');
    }

    function filtrationServices($prix_min,$prix_max,$titre,$dt,$localisation) {
    
        $filters = [
            'prix_min'    => $prix_min,
            'prix_max' => $prix_max+1,        
            'titre'    => $titre,
            'dt' => $dt,        
            'localisation'    => $localisation,
        ];
     
      $res = App\Services::where(function ($query) use ($filters) {
        if ($filters['prix_min']!='all') {
            
            $query->where('salaire_min', '=', $filters['prix_min']);
        }
    
        if ($filters['prix_max']!='all') {
                $query->where('salaire_max', '<', $filters['prix_max']);
        }
        if ($filters['titre']!='all') {
            return $filters['titre'];
                $query->where('titre', '=', $filters['titre']);
        }
        
        if ($filters['dt']!='all') {
                    $query->where('created_at', 'like', $filters['dt']);
        }
             
        if ($filters['localisation']!='all') {
            
            
                $query->where('localisation', '=', $filters['localisation']);
        }
        })->get();
    
    
    
    
    
    
    
    
    
    
    
    
         
         return $res;
    }
}
