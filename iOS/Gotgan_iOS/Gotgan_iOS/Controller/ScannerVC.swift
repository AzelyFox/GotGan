//
//  QRCodeScan.swift
//  Gotgan_iOS
//
//  Created by Gon on 2019/12/02.
//  Copyright © 2019 reqon. All rights reserved.
//

import Foundation
import SwiftUI
import UIKit

struct ScannerVC: UIViewControllerRepresentable {
    @Binding var showAlert: Bool
    @Binding var code: String
    
    func makeCoordinator() -> Coordinator {
        Coordinator(self)
    }
    
    func makeUIViewController(context: Context) -> ScannerViewController {
        let vc = ScannerViewController()
        vc.delegate = context.coordinator
        return vc
    }
    
    func updateUIViewController(_ vc: ScannerViewController, context: Context) {
        
    }

    class Coordinator: NSObject, QRCodeScannerDelegate {
        var parent: ScannerVC
        
        init(_ parent: ScannerVC) {
            self.parent = parent
        }
        
        func codeDidFind(_ code: String) {
            print(code)
            //let ac = UIAlertController(title: "Code Found", message: code, preferredStyle: .alert)
            //ac.addAction(UIAlertAction(title: "OK", style: .default))
            //present(ac, animated: true)
            //captureSession = nil
            // show alert and go rent detail view
            parent.showAlert = true
            parent.code = code
        }
    }
}

