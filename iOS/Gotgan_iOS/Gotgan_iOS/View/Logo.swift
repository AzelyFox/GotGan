//
//  LogoView.swift
//  Gotgan_iOS
//
//  Created by Gon on 2019/11/26.
//  Copyright © 2019 reqon. All rights reserved.
//

import SwiftUI

struct Logo: View {
    var body: some View {
        VStack
        {
            Image("Logo_Gotgan")
            .resizable()
            .frame(width: 692/3,height:696/3)
            Text("메이커 스페이스 재고관리 어플").offset(y: -80)
        }
    }
}

struct LogoView_Previews: PreviewProvider {
    static var previews: some View {
        LogoImage()
    }
}
