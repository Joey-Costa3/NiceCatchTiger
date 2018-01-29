//
//  infoViewController.swift
//  NiceCatch
//
//  Created by Joey Costa on 12/2/15.
//  Copyright © 2015 Joey Costa. All rights reserved.

import Foundation
import UIKit

class infoViewController: UIViewController, UIGestureRecognizerDelegate, UITextViewDelegate {
    
    @IBOutlet weak var infoLabel: UILabel!
	var timer = Timer();
	@IBOutlet var infoTextBox: UITextView!
    override func viewDidLoad() {
        super.viewDidLoad()
		self.navigationItem.title = ""

        myMutableString = NSMutableAttributedString(attributedString: infoTextBox.attributedText)
		
//        myMutableString.addAttribute(NSForegroundColorAttributeName, value: UIColor.blue, range: NSRange(location:myString.length - 21,length: 21))

		let _ = myMutableString.setAsLink(textToFind: "confidential hotline", linkURL: "http://www.clemson.edu/administration/internalaudit/ethicsline.html")
		
		infoTextBox.attributedText = myMutableString
		//hyperlink.attributedText = myMutableString
//        
//        let tap:UITapGestureRecognizer = UITapGestureRecognizer(target: self, action: #selector(infoViewController.labelAction))
//        hyperlink.addGestureRecognizer(tap)
//        tap.delegate = self // Remember to extend your class with UIGestureRecognizerDelegate
		
		
		//infoTextBox.text = "\(infoTextBox.text)\n\n\(myMutableString)"
		
		
		timer = Timer.scheduledTimer(timeInterval: 3, target: self, selector: #selector(infoViewController.flash), userInfo: nil, repeats: true)
		timer.fire()
    }
	
	
	
	override func viewDidLayoutSubviews() {
		flash()
	}
	func flash()
	{
		//print("Flash scroll")
		infoTextBox.flashScrollIndicators()

	}
	override func viewDidAppear(_ animated: Bool) {
		flash()
	}
	override func viewWillDisappear(_ animated: Bool) {
		//print("Stop timer")
		timer.invalidate()
	}
    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
	override func viewWillAppear(_ animated: Bool) {
		DispatchQueue.main.async(execute: {
			self.infoTextBox.scrollRangeToVisible(NSMakeRange(0, 0))
		})
		

	}
    @IBOutlet weak var hyperlink: UILabel!
    var myString:NSString = "If you have a serious safety or ethical concern but do not feel comfortable sharing your identity, please use the confidential hotline."
    var myMutableString = NSMutableAttributedString()
    
    // Receive action
    func labelAction()
    {
        UIApplication.shared.openURL(NSURL(string:"http://www.clemson.edu/administration/internalaudit/ethicsline.html")! as URL)
    }
        
}


extension NSMutableAttributedString {
	
	public func setAsLink(textToFind:String, linkURL:String) -> Bool {
		
		let foundRange = self.mutableString.range(of: textToFind)
		if foundRange.location != NSNotFound {
			self.addAttribute(NSLinkAttributeName, value: linkURL, range: foundRange)
			return true
		}
		return false
	}
}
