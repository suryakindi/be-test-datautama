<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
use App\Models\Transactions;    


class DataController extends Controller
{
    public function listproduct(){
        $product = Product::all();
        return response([
            'message' => 'Sukses Mendapatkan Data Product',
            'data' => $product,
        ], 200);
    }

    public function createproduct(Request $request){
        $request->validate([
            'name' => 'required',
            'price' => 'required|integer',
            'stock'=> 'required|integer',
            'description' => 'required',
        ]);
        if($request){
            $product = new Product;
            $product->name = $request->name;
            $product->price = $request->price;
            $product->stock = $request->stock;
            $product->description = $request->description;
            $product->save();
            return response([
                'message' => 'Sukses Menambah Data Product',
                'data' => $product,
            ], 200);
        }else{
            return response([
                'message' => 'Gagal Menambah Data Product',
                'data' => $product,
            ], 200);  
        }
    }
    public function getproduct($id){
        $product = Product::find($id);
        return response([
            'message' => 'Sukses Get Data Product',
            'data' => $product,
        ], 200);
    }
    public function editproduct(Request $request, $id){
        $request->validate([
            'name' => 'required',
            'price' => 'required|integer',
            'stock'=> 'required|integer',
            'description' => 'required',
        ]);
        if($request){
            $product = Product::find($id);
            if($product == null){
                return response([
                    'message' => 'Data Tidak Ditemukan',
                    'data' => $product,
                ], 404);
            }else{
                $product->name = $request->name;
                $product->price = $request->price;
                $product->stock = $request->stock;
                $product->description = $request->description;
                $product->save();
                return response([
                    'message' => 'Sukses Edit Data Product',
                    'data' => $product,
                ], 200);
            }
           
        }else{
            return response([
                'message' => 'Gagal Edit Data Product',
                'data' => $product,
            ], 200);  
        }

    }

    public function deleteproduct($id){
        $product = Product::find($id);
        if($product != null){
            $product->delete();
            return response([
                'message' => 'Sukses Hapus Data Product',
                'date' => date('d-M-Y'),
            ], 200);
        }else{
            return response([
                'message' => 'Gagal Hapus Data Product',
                'data' => $product,
            ], 200);  
        }
    }

    public function listtransactions(){
        $transactions = Transactions::all();
        return response([
            'message' => 'Sukses Mendapatkan Data Transactions',
            'data' => $transactions,
        ], 200);
    }

    public function transactionsproduct(Request $request){
        if (!$request->has('quantity') || !$request->has('product_id')) {
            return response()->json(['error' => 'Invalid Parameter.'], 400);
        } else {
            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer|min:1',
                'product_id' => 'required|integer|min:1',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => 'Ada beberapa parameter yang tidak valid.'], 400);
            } else {
               $product = Product::find($request->input('product_id'));
                if($product == null){
                    return response()->json(['error' => 'Data Product Tidak Ditemukan.'], 404);
                }else{
                   $price = $product->price;
                   $payment_amount = $request->input('quantity') * $price;
                   $client = new Client();
                   $apiKey = 'DATAUTAMA';
                   $stringToHash = 'POST:' . $apiKey;
                   $signature = hash('sha256', $stringToHash);
                  
                    $headers = [
                        'X-API-KEY' => $apiKey,
                        'X-SIGNATURE' => $signature,
                     ];
                     
                     try {
                        $response = $client->post('http://tes-skill.datautama.com/test-skill/api/v1/transactions', [
                            'headers' => $headers,
                            'form_params' => [
                                'quantity' => $request->input('quantity'),
                                'price' => $price,
                                'payment_amount' => $payment_amount,
                            ],
                        ]);

                        $responseBody = $response->getBody()->getContents();
                        $responseData = json_decode($responseBody);
                        $transactions = new Transactions;
                        $transactions->reference_no = $responseData->data->reference_no;
                        $transactions->price = $price;
                        $transactions->payment_amount = $payment_amount;
                        $transactions->product_id = $product->id;
                        $transactions->save();
                        return response()->json([
                            'message' => 'OK',
                            'reference_no' => $transactions->reference_no,
                            'quantity' => $request->input('quantity'),
                            'price' => $price,
                            'payment_amount' => $payment_amount,
                            'product_id' => $product->id,
                        
                        ], 200);


                    } catch (Exception $e) {
                    
                        return response()->json(['error' => 'Request failed.'], 500);
                    }
                    
                }
               
            }
        }
    }
}
