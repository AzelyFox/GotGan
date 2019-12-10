//
//  RentListModel.swift
//  Gotgan_iOS
//
//  Created by Gon on 2019/12/01.
//  Copyright © 2019 reqon. All rights reserved.
//

import Foundation

struct Rents: Codable, Hashable
{
    let rent_index: Int
    //대여 인덱스를 의미한다.
    
    let rent_user_index: Int
    //대여한 유저의 인덱스를 의미한다.

    let rent_user_name: String
    //대여한 유저의 이름을 의미한다.

    let rent_user_id: String
    //대여한 유저의 아이디를 의미한다.

    let rent_product_index: Int
    //대여한 물품의 인덱스를 의미한다.

    let rent_product_group_index: Int
    //대여한 물품의 물품 그룹 인덱스를 의미한다.

    let rent_product_group_name: String
    //대여한 물품의 물품 그룹명을 의미한다.

    let rent_product_name: String
    //대여한 물품의 개별 이름을 의미한다.

    let rent_product_barcode: Int?
    //대여한 물품의 바코드 번호를 의미한다.

    let rent_status: Int
    //대여 상태를 의미한다.
    // 0 : 완료됨
    // 1 : 대여 신청됨
    // 2 : 대여중

    let rent_time_start: String
    //대여 시작 일자를 의미한다.
    //대여 신청 상태일 경우, 대여를 신청한 시각이다.
    //대여중인 상태일 경우, 관리자가 대여를 허가한 시각이다.
    //yyyy-MM-dd HH:mm:ss형식이다.

    let rent_time_end: String?
    //대여 만료 일자를 의미한다.
    //yyyy-MM-dd HH:mm:ss형식이다.

    let rent_time_return: String?
    //대여 반납 일자를 의미한다
    //yyyy-MM-dd HH:mm:ss형식이다.
    
    enum CodingKeys: String, CodingKey{
        case rent_index
        case rent_user_index
        case rent_user_name
        case rent_user_id
        case rent_product_index
        case rent_product_group_index
        case rent_product_group_name
        case rent_product_name
        case rent_product_barcode
        case rent_status
        case rent_time_start
        case rent_time_end
        case rent_time_return
    }
    
    init(from decoder:Decoder) throws{
        let values = try decoder.container(keyedBy: CodingKeys.self)
        rent_index = try values.decode(Int.self, forKey: .rent_index)
        rent_user_index = try values.decode(Int.self, forKey: .rent_user_index)
        rent_user_name = try values.decode(String.self, forKey: .rent_user_name)
        rent_user_id = try values.decode(String.self, forKey: .rent_user_id)
        rent_product_index = try values.decode(Int.self, forKey: .rent_product_index)
        rent_product_group_index = try values.decode(Int.self, forKey: .rent_product_group_index)
        rent_product_group_name = try values.decode(String.self, forKey: .rent_product_group_name)
        rent_product_name = try values.decode(String.self, forKey: .rent_product_name)
        rent_product_barcode = try values.decodeIfPresent(Int.self, forKey: .rent_product_barcode)
        rent_status = try values.decode(Int.self, forKey: .rent_status)
        rent_time_start = try values.decode(String.self, forKey: .rent_time_start)
        rent_time_end = try values.decode(String.self, forKey: .rent_time_end)
        rent_time_return = try values.decode(String.self, forKey: .rent_time_return)
    }
}
