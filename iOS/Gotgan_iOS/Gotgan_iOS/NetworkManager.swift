//
//  NetworkManager.swift
//  Gotgan_iOS
//
//  Created by Gon on 2019/11/04.
//  Copyright © 2019 reqon. All rights reserved.
//

import Foundation
import Alamofire
import SwiftyJSON

public class NetworkManager
{
    static let Instance = NetworkManager()
    
    private let url:String = "https://api.devx.kr/GotGan/v1/"
    private let login:String = "login.php"
    private let test:String = "test.php"
    
    public func RequestTest(value:String)
    {
        let param = ["value":value] as Parameters
        AF.request(url + test,method: .post,parameters: param, encoding: URLEncoding.default)
        .validate()
        .responseData(completionHandler:
        {
            response in
            switch response.result
            {
            case .success(let data):
                print("Validation Succeded")
                let result = String(decoding:data, as: UTF8.self)
                print("\(result)")
                
            case .failure(let error):
                print("Validation Fail")
                print(error)
                
            }
        })
    }
    
    public func RequestLogin(id:String,pw:String)
    {
        if id == "" || pw == ""{
            print("You didn't enter id or password")
            return
        }
        
        let param = ["user_id":id,"user_pw":pw] as Parameters
        AF.request(url+login, method: .post, parameters: param, encoding:URLEncoding.default)
            .validate(contentType: ["text/html"])
            .responseJSON(completionHandler:
            {
                response in
                switch response.result
                {
                case .success(let data):
                    print("Validation Succeded")
                    let result = JSON(data)
                    print("Recieved Data : \(result)")
                    
                case .failure(let error):
                    print("Validation Failed")
                    print(error)
                    
                }
            })
    }
}
