//
//  User.swift
//  Gotgan_iOS
//
//  Created by Gon on 2019/11/05.
//  Copyright © 2019 reqon. All rights reserved.
//

import Foundation
//import SwiftyJSON
let USER_DEBUG = 0

struct User: Codable{
    let session:String?
    let index:Int?
    let id:String?
    let group_index:Int?
    let group_name:String?
    let level:Int?
    let name:String?
    let created:String?
    let sid:String?
    let block:Int?
    let email:String?
    let phone:String?
    
    enum CodingKeys: String, CodingKey
    {
        case session = "session"
        case index = "user_index"
        case id = "user_id"
        case group_index = "user_group_index"
        case group_name = "user_group_name"
        case level = "user_level"
        case name = "user_name"
        case created = "user_created"
        case sid = "user_sid"
        case block = "user_block"
        case email = "user_email"
        case phone = "user_phone"
    }
    
    init(from decoder: Decoder) throws
    {
        let values = try decoder.container(keyedBy: CodingKeys.self)
        session = try values.decodeIfPresent(String.self, forKey: .session)
        index = try values.decodeIfPresent(Int.self,forKey: .index)
        id = try values.decodeIfPresent(String.self, forKey: .id)
        group_index = try values.decodeIfPresent(Int.self, forKey: .group_index)
        group_name = try values.decodeIfPresent(String.self, forKey: .group_name)
        level = try values.decodeIfPresent(Int.self, forKey: .level)
        name = try values.decodeIfPresent(String.self, forKey: .name)
        created = try values.decodeIfPresent(String.self, forKey: .created)
        sid = try values.decodeIfPresent(String.self, forKey: .sid)
        block = try values.decodeIfPresent(Int.self, forKey: .block)
        email = try values.decodeIfPresent(String.self, forKey: .email)
        phone = try values.decodeIfPresent(String.self, forKey: .phone)
    }
}

/*
public class User
{
    let session:String
    let index:Int
    let id:String
    let group_index:Int
    let group_name:String
    let level:Int
    let name:String
    let created:String
    let sid:String?
    let block:Int?
    let email:String?
    let phone:String?
    
    init(session:String,index:Int,level:Int,id:String,name:String,group_index:Int,group_name:String,created:String,sid:String,email:String,phone:String,block:Int)
    {
        self.session = session
        self.index = index
        self.id = id
        self.level = level
        self.name = name
        self.group_index = group_index
        self.group_name = group_name
        self.created = created
        self.sid = sid
        self.email = email
        self.phone = phone
        self.block = block
    }
    
    class func build(json:JSON) -> User?
    {
        #if USER_DEBUG
         print(" \(String(describing: json["session"].string))\n \(String(describing: json["user_index"].int))\n \(String(describing: json["user_id"].string))\n \(String(describing: json["user_level"].int))\n \(String(describing: json["user_name"].string))\n \(String(describing: json["user_group_index"].int))\n \(String(describing: json["user_group_name"].string))\n \(String(describing: json["user_created"].string))\n \(String(describing: json["user_sid"].string))\n \(String(describing: json["user_email"].string))\n \(String(describing: json["user_phone"].string))\n \(String(describing: json["user_block"].int))\n")
        #endif
        if
            let _session = json["session"].string,
            let _index = json["user_index"].int,
            let _id = json["user_id"].string,
            let _level = json["user_level"].int,
            let _name = json["user_name"].string,
            let _group_index = json["user_group_index"].int,
            let _created = json["user_created"].string,
            let _sid = json["user_sid"].string,
            let _email = json["user_email"].string,
            let _phone = json["user_phone"].string,
            let _block = json["user_block"].int
        {
            if let _group_name = json["user_group_name"].string
            {
                return User(session: _session, index: _index, level: _level, id: _id, name: _name, group_index: _group_index, group_name: _group_name, created: _created, sid: _sid, email: _email, phone: _phone, block: _block)
            }
            else
            {
                return User(session: _session, index: _index, level: _level, id: _id, name: _name, group_index: _group_index, group_name: "", created: _created, sid: _sid, email: _email, phone: _phone, block: _block)
            }
         
        }
        else
        {
            #if USER_DEBUG
            print("Bad json")
            #endif
            return nil
        }
    }
}
*/
