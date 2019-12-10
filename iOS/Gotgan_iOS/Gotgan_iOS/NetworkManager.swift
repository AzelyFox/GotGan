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
    private let register:String = "user_add.php"
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
                print("Test Succeded")
                let result = String(decoding:data, as: UTF8.self)
                print("\(result)")
                
            case .failure(let error):
                print("Test Fail")
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
                    print("Login Succeded")
                    let result = JSON(data)
                    print("Recieved Data : \(result)")
                    
                case .failure(let error):
                    print("Login Failed")
                    print(error)
                    
                }
            })
    }
    
    let USER:Int = 0
    let ADMIN:Int = 1
    let MASTER:Int = 2
    public func RequestRegister(id:String,pw:String)
    {
        let level:Int = USER
        let name:String = ""
        let group:Int = 0
        let sid:String = ""
        let blocked:Int = 0
        let uuid:String = ""
        let email:String = ""
        let phone:String = ""
        
        let param:[String:Any] = ["user_id":id,
                                  "user_pw":pw,
                                  "user_level":level,
                                  "user_name":name,
                                  "user_group":group,
                                  "user_sid":sid,
                                  "user_block":blocked,
                                  "user_uuid":uuid,
                                  "user_email":email,
                                  "user_phone":phone]
        
        AF.request(url+register, method: .post, parameters: param, encoding:URLEncoding.default)
        .validate(contentType: ["text/html"])
        .responseJSON(completionHandler:
        {
            response in
            switch response.result
            {
            case .success(let data):
                print("Register Succeded")
                let result = JSON(data)
                print("Recieved Data : \(result)")
                
            case .failure(let error):
                print("Register Failed")
                print(error)
                
            }
        })
    }
}
