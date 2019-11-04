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
    
    private let url:String = "https://devx.kr/Apps/GotGan/"
    private let login:String = "login.php"
    private let test:String = "test.php"
    
    public func RequestLogin(id:String,pw:String)
    {
        if id == "" || pw == ""{
            print("You didn't enter id or password")
            return
        }
        
        let param = ["user_id":id,"user_pw":pw]
        AF.request(url+test,method: .post,parameters: param,encoding:JSONEncoding.default)
            .validate(contentType: ["text/html"])
            .responseJSON(completionHandler:
            {
                response in switch response.result
                {
                case .success:
                    print("Validation Success")
                    let value = response.result
                    print(value)
                    
                case .failure(let error):
                    print("Validation Failure")
                    print(error)
                    
                }
            })
    }
}
