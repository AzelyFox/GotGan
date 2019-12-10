//
//  HistoryView.swift
//  Gotgan_iOS
//
//  Created by Gon on 2019/11/15.
//  Copyright © 2019 reqon. All rights reserved.
//

import SwiftUI
import Material

struct HistoryView: SwiftUI.View {
    var body: some SwiftUI.View {
        VStack
            {
                HStack
                {
                    Text("History")
                    .foregroundColor(Color.white)
                }
                .frame(minWidth: 0, maxWidth: .infinity, minHeight: 0, maxHeight: 50,alignment: .center)
                .background(Color.orange)

        }.frame(minWidth: 0, maxWidth: .infinity, minHeight: 0, maxHeight: .infinity,alignment: .top)
    }
}

struct HistoryView_Previews: PreviewProvider {
    static var previews: some SwiftUI.View {
        HistoryView()
    }
}
