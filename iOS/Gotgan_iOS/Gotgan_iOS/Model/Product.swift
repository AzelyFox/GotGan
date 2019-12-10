//
//  Product.swift
//  Gotgan_iOS
//
//  Created by Gon on 2019/11/20.
//  Copyright © 2019 reqon. All rights reserved.
//

import Foundation
import SwiftUI
import SwiftyJSON
let PRODUCT_DEBUG = 0
class Product: Codable
{
    let product_index:Int
    let product_group:Int
    let product_name:String
    let product_status:Int
    let product_owner:Int
    let product_rent:Int
    let product_barcode:Int
    let product_created:String
    var product_rent_index:Int?
    var product_rent_status:Int?
    var product_rent_user_index:Int?
    var product_rent_user_id:String?
    var product_rent_user_name:String?
    var product_rent_time_start:String?
    var product_rent_time_end:String?
    init(index:Int,group:Int,name:String,status:Int,owner:Int,rent:Int,barcode:Int,created:String)
    {
        product_name = name
        product_rent = rent
        product_group = group
        product_index = index
        product_owner = owner
        product_barcode = barcode
        product_created = created
        product_status = status
    }
    
    class func build(json: JSON) -> Product?
    {
        #if PRODUCT_DEBUG
        print("build with \(json)")
        #endif
        if
            let index = json["product_index"].int,
            let group = json["product_group_index"].int,
            let name = json["product_name"].string,
            let status = json["product_status"].int,
            let owner = json["product_owner_index"].int,
            let rent = json["product_rent_index"].int,
            let barcode = json["product_barcode"].int,
            let created = json["product_created"].string
        {
            let product = Product(index: index, group: group, name: name, status: status, owner: owner, rent: rent, barcode: barcode, created: created)
            if
                let rent_index = json["product_rent_index"].int,
                let rent_status = json["product_rent_status"].int,
                let rent_user_index = json["product_rent_user_index"].int,
                let rent_user_id = json["product_rent_user_id"].string,
                let rent_user_name = json["product_rent_user_name"].string,
                let product_rent_time_start = json["product_rent_time_start"].string,
                let product_rent_time_end = json["product_rent_time_end"].string
            {
                product.product_rent_index = rent_index
                product.product_rent_user_index = rent_user_index
                product.product_rent_user_id = rent_user_id
                product.product_rent_user_name = rent_user_name
                product.product_rent_status = rent_status
                product.product_rent_time_start = product_rent_time_start
                product.product_rent_time_end = product_rent_time_end
            }
            
            return product
        }
        else
        {
            print("Product.cs initialization failed : Bad JSON")
            return nil
            
        }
    }
}

class ProductGroup
{
    let group_index:Int
    let group_name:String
    let group_rentable:Int
    let group_priority:Int
    let group_count_available:Int
    let group_count_unavailable:Int
    let group_count_broken:Int
    let group_count_rent:Int
    let group_count_repair:Int
    
    init(index:Int,name:String,rentable:Int,priority:Int,available:Int,unavailable:Int,broken:Int,rent:Int,repair:Int)
    {
        self.group_index = index
        self.group_name = name
        self.group_priority = priority
        self.group_rentable = rentable
        self.group_count_available = available
        self.group_count_unavailable = unavailable
        self.group_count_rent = rent
        self.group_count_broken = broken
        self.group_count_repair = repair
    }
    
    class func build(json:JSON) -> ProductGroup?
    {
        #if PRODUCT_DEBUG
        print("build with \(json)")
        #endif
        if
            let index = json["group_index"].int,
            let name = json["group_name"].string,
            let priority = json["group_priority"].int,
            let rentable = json["group_rentable"].int,
            let available = json["group_count_available"].int,
            let unavailable = json["group_count_unavailable"].int,
            let rent = json["group_count_rent"].int,
            let broken = json["group_count_broken"].int,
            let repair = json["group_count_repair"].int
        {
            return ProductGroup(index: index, name: name, rentable: rentable, priority: priority, available: available, unavailable: unavailable, broken: broken, rent: rent, repair: repair)
        }
        else
        {
            print("ProuctGroup.cs initialization failed : Bad JSON")
            return nil
        }
    }
    
}

class ProductManager
{
    static let Instance = ProductManager()
    private init(){}
    
    public var ProductList = [Product]()
    public var ProductOverView = [ProductGroup]()
    
    func LoadAllProducts(json: JSON)
    {
        #if PRODUCT_DEBUG
        print("LoadProducts : received JSON\n \(json)")
        print("LoadProducts : sub JSON\n")
        #endif
        for (index, sub):(String,JSON) in json
        {
            if let product = Product.build(json: sub)
            {
                ProductList.append(product)
            }
        }
        #if PRODUCT_DEBUG
        print(" = = = = = = = Product List  = = = = = = = \n")
        for product:Product in ProductList
        {
            print(" \(product.product_index) \(product.product_name)")
        }
        #endif
    }
    
    func LoadProductOverView(json: JSON)
    {
        for (index, sub):(String,JSON) in json
        {
            if let product_group = ProductGroup.build(json: sub)
            {
                ProductOverView.append(product_group)
            }
        }
    }
}
