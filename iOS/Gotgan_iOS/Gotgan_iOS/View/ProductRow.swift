//
//  ProductRow.swift
//  Gotgan_iOS
//
//  Created by Gon on 2019/11/26.
//  Copyright © 2019 reqon. All rights reserved.
//

import SwiftUI
import Foundation

struct ProductRow: View {
    var product: Product
    
    var body: some View {
        HStack
        {
            Image("baseline_done_black_18dp")
            .background(Color.green)
            .padding(20)
            //.border(Color.orange,width:1)
            
            VStack(alignment: .leading)
            {
                Text("\(product.product_name)")
                .font(.title)
                HStack
                {
                    
                    Text("id \(String(product.product_barcode))")
                    .font(.subheadline)
                    .foregroundColor(.secondary)
                    Spacer()
                }
            }
            .padding(5)
            Spacer()
            Text("\(product.product_rent)")
            .padding(20)
        }
    }
}

let sample = Product(index: 1, group: 1, name: "Aduino Kit", status: 1, owner: 1, rent: 1, barcode: 10000, created: "2019-11-26 21:07:11")



struct ProductRow_Previews: PreviewProvider {
    static var previews: some View {
        ProductRow(product: sample)
    }
}
