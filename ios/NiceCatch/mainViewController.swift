//
//  ViewController.swift
//  NiceCatch
//
//  Created by Joey Costa on 11/8/15.
//  Copyright © 2015 Joey Costa. All rights reserved.


import UIKit
import Foundation
import SwiftECP
import XCGLogger
import Result
import ReactiveSwift

class mainViewController: UIViewController, UIGestureRecognizerDelegate, UITextFieldDelegate {
    
	@IBOutlet var niceCatchLabel: UILabel!
	
	
	@IBOutlet var reportNiceCatch: UIButton!
	@IBOutlet var aboutButton: UIButton!
	var doubleTapGesture: UITapGestureRecognizer! = nil
	var holdGesture: UILongPressGestureRecognizer! = nil
	var tapGesture: UITapGestureRecognizer! = nil
	var recognizeHold: Bool = false
	var dataForNextView: Int = 0
	var presentedNoData = false
	var loadingIndicator: UIActivityIndicatorView = UIActivityIndicatorView(activityIndicatorStyle: .whiteLarge)
	var alert: UIAlertController? = nil
	
	// login view outlets
	@IBOutlet var loginView: UIView!
	@IBOutlet var CUusername: UITextField!
	@IBOutlet var CUpassword: UITextField!
	var blurView: UIVisualEffectView?
	var backgroundTouched: UITapGestureRecognizer!
	var blurbackgroundTouched: UITapGestureRecognizer!
	@IBOutlet var ClemsonLogin: UIButton!
	@IBOutlet var dismissLogin: UIButton!
	@IBOutlet var usernameLabel: UILabel!
	@IBOutlet var passwordLabel: UILabel!
	var keyboardShown = false
	let loginMoveOnKeyboard: CGFloat = 64.0
	@IBOutlet var loginViewCenterY: NSLayoutConstraint!
	var successfulLogin = false
	var invalidCredentials = false
	var loginViewCenterYValue: CGFloat = 0.0
	
	@IBOutlet var versionNumber: UILabel!
	
	@IBOutlet var versionLabel: UILabel!
	
    override func viewDidLoad() {
        super.viewDidLoad()
		clearData()

		if let version = Bundle.main.infoDictionary?["CFBundleShortVersionString"] as? String {
			self.versionNumber.text = "Version " + version
		}
		//print("THE VALUE FOR function is \(setECPRequest())\n\n\n\n\n\n\n")
		reportNiceCatch.layer.cornerRadius = 5
		aboutButton.layer.cornerRadius = 5
		self.loginView.layer.cornerRadius = 5
		self.ClemsonLogin.layer.cornerRadius = 5
		self.dismissLogin.layer.cornerRadius = 5
		        definesPresentationContext = true
        // Do any additional setup after loading the view, typically from a nib.
		
		// add gesture recognizers to objects
		holdGesture = UILongPressGestureRecognizer(target: self, action: #selector(mainViewController.aboutButtonHeld))
		holdGesture.minimumPressDuration = 1
		holdGesture.delegate = self
		aboutButton.addGestureRecognizer(holdGesture)
		
		tapGesture = UITapGestureRecognizer(target: self, action: #selector(mainViewController.toggleVersion))
		tapGesture.numberOfTapsRequired = 3
		versionNumber.addGestureRecognizer(tapGesture)
		
		NotificationCenter.default.addObserver(self, selector: #selector(mainViewController.reportKindSuccess), name: NSNotification.Name(rawValue: "reportKindSuccess"), object: nil)
		NotificationCenter.default.addObserver(self, selector: #selector(mainViewController.involvementKindSuccess), name: NSNotification.Name(rawValue: "involvementKindSuccess"), object: nil)
		NotificationCenter.default.addObserver(self, selector: #selector(mainViewController.keyboardWillShow), name: NSNotification.Name.UIKeyboardWillShow, object: nil)
		NotificationCenter.default.addObserver(self, selector: #selector(mainViewController.keyboardWillHide), name: NSNotification.Name.UIKeyboardWillHide, object: nil)
		CUusername.delegate = self
		CUpassword.delegate = self
		
		backgroundTouched = UITapGestureRecognizer(target: self, action: #selector(mainViewController.hideKeyboard))
		backgroundTouched.delegate = self
		loginView.addGestureRecognizer(backgroundTouched!)
		
    }
	func setECPRequest(){
		self.successfulLogin = false
		let username = self.CUusername.text ?? ""
		let password = self.CUpassword.text ?? ""
		self.CUpassword.text = ""
		let protectedURL = URL(
			string: "https://nicecatchtiger.com/authen.php"
			)!
		let logger = XCGLogger()
		logger.setup(level: .debug)
	
		ECPLogin(
			protectedURL: protectedURL,
			username: username,
			password: password,
			logger: logger
			).start { event in
	switch event {
		
	case let .value(body):
		// If the request was successful, the protected resource will
		// be available in 'body'. Make sure to implement a mechanism to
		// detect authorization timeouts.
		print("Response body: \(body)")
		
		// The Shibboleth auth cookie is now stored in the sharedHTTPCookieStorage.
		// Attach this cookie to subsequent requests to protected resources.
		// You can access the cookie with the following code:
		if let cookies = HTTPCookieStorage.shared.cookies {
			let shibCookie = cookies.filter { (cookie: HTTPCookie) in
				cookie.name.range(of: "shibsession") != nil
				}[0]
			print(shibCookie)
			preload() //load the pickers for the next views
			self.invalidCredentials = false
			self.successfulLogin = true
			self.dismissLogin.sendActions(for: .touchUpInside)
			finalReportData.username = self.CUusername.text!

		}
		
	case let .failed(error):
		// This is an NSError containing both a user-friendly message and a
		// technical debug message. This can help diagnose problems with your
		// SP, your IdP, or even this library :)
		
		//print("Error: ")
		// User-friendly error message
		print(error.description)
		
		//print("Debug: ")
		// Technical/debug error message
		print(error.error.localizedDescription)
		self.invalidCredentials = true
		let alert = UIAlertController(title: "Invalid credentials", message: "The supplied username or password were incorrect. Use your Clemson username (no @clemson.edu), and your password to use this application.", preferredStyle: .alert)
		alert.addAction(UIAlertAction(title: "Ok", style: .default, handler: {
			action in

			//self.dismissLogin.sendActions(for: .touchUpInside)
			self.CUusername.resignFirstResponder()
			self.CUpassword.resignFirstResponder()
		}))
		self.present(alert, animated: true, completion: nil)
		//self.authenticate()
		
	default:
		self.invalidCredentials = true

		break

    }
		}
		//return login

	}
	
	func authenticate(){
		let blur = UIBlurEffect(style: .extraLight)
		blurView = UIVisualEffectView(effect: blur)
		self.blurView?.alpha = 0.0
		blurView?.tag = 404
		let niceCatchLabelHeight: CGFloat = niceCatchLabel.frame.size.height
		blurView?.frame = CGRect(origin: CGPoint(x: 0, y: niceCatchLabelHeight), size: CGSize(width: view.frame.size.width, height: view.frame.size.height - niceCatchLabelHeight))
		blurView?.autoresizingMask = [.flexibleWidth, .flexibleHeight]
		//view.addSubview(blurView!)
		
		blurbackgroundTouched = UITapGestureRecognizer(target: self, action: #selector(mainViewController.hideKeyboard))
		blurbackgroundTouched.delegate = self
		blurView?.addGestureRecognizer(blurbackgroundTouched!)
		//self.loginView.isHidden = false
		
		self.view.addSubview(blurView!)
		self.view.bringSubview(toFront: self.loginView)
		self.loginView.isHidden = false

		self.loginViewCenterY.constant = self.loginViewCenterYValue
		UIView.animate(withDuration: 0.5, delay: 0.0, options: UIViewAnimationOptions.curveEaseInOut, animations: {
			self.loginViewVisible()
			self.loginView.layoutIfNeeded()
//			self.loginView.center = CGPoint(x: self.view.frame.size.width/2, y: self.view.frame.size.height/2)
		}, completion:nil)
		
	}
	
	
	@IBAction func attemptLogin(_ sender: Any) {
		let reach: Reach = Reach.forInternetConnection()
		if reach.currentReachabilityStatus().rawValue == NotReachable.rawValue {
			self.displayConnectionError()
		} else {
			self.setECPRequest()
		}
		
		//setECPRequest()
//		loginView.isHidden = true
//		if let blur = self.view.viewWithTag(404){
//			blur.removeFromSuperview()
//		}
		

	}
	
	
	func reportKindSuccess(notification: NSNotification)
	{
		dataForNextView = dataForNextView + 1
		if(dataForNextView == 2)
		{
			dismissLoadingView()
			
		}
		
	}
	func involvementKindSuccess(notification: NSNotification)
	{
		dataForNextView = dataForNextView + 1
		if (dataForNextView == 2)
		{
			dismissLoadingView()
			
		}
	
	}
	func hideKeyboard(){
		self.view.endEditing(true)

	}
	// move the login view up some to start editing
	func keyboardWillShow(notification: NSNotification) {
	 if let _ = (notification.userInfo?[UIKeyboardFrameBeginUserInfoKey] as? NSValue)?.cgRectValue {
		if (!keyboardShown){
			self.loginViewCenterY.constant -= self.loginMoveOnKeyboard
			UIView.animate(withDuration: 0.3, animations: {
				self.loginView.layoutIfNeeded()
				

			})
			keyboardShown = true
			}
		}
	}
	// move the login view down some to end editing
	func keyboardWillHide(notification: NSNotification) {
		if let _ = (notification.userInfo?[UIKeyboardFrameBeginUserInfoKey] as? NSValue)?.cgRectValue {
			if (keyboardShown){
				self.loginViewCenterY.constant += self.loginMoveOnKeyboard
				UIView.animate(withDuration: 0.3, animations: {
					self.loginView.layoutIfNeeded()
				})
				keyboardShown = false
			}
		}

	}
	
	func textFieldShouldReturn(_ textField: UITextField) -> Bool {
		if textField == loginView.viewWithTag(3){
			CUpassword.becomeFirstResponder()
		}
		if textField == loginView.viewWithTag(4){
			textField.resignFirstResponder()
			ClemsonLogin.sendActions(for: .touchUpInside)
		}
		return false
	}
	
	func dismissLoadingView()
	{
		loadingIndicator.isHidden = true
		loadingIndicator.stopAnimating()
		if(presentedNoData){
			alert?.dismiss(animated: true, completion: {
				self.performSegue(withIdentifier: "reportView", sender: self)
				
			})
			
		}else{
			if(successfulLogin){
				reportNiceCatch.sendActions(for: .touchUpInside)
			}
		}
	}
	func toggleVersion(){
		self.versionLabel.isHidden = !self.versionLabel.isHidden
	}
	
	func loginViewOpaque(){
		
		self.loginView.alpha = 0.0
		self.CUusername.alpha = 0.0
		self.CUpassword.alpha = 0.0
		self.usernameLabel.alpha = 0.0
		self.passwordLabel.alpha = 0.0
		self.ClemsonLogin.alpha = 0.0
		self.dismissLogin.alpha = 0.0
		self.blurView?.alpha = 0.0
	}
	func loginViewVisible(){
		
		self.loginView.alpha = 1.0
		self.CUusername.alpha = 1.0
		self.CUpassword.alpha = 1.0
		self.usernameLabel.alpha = 1.0
		self.passwordLabel.alpha = 1.0
		self.ClemsonLogin.alpha = 1.0
		self.dismissLogin.alpha = 1.0
		self.blurView?.alpha = 1.0

	}
	
	@IBAction func dismissLoginView(_ sender: Any) {
		//		loginView.isHidden = true
		//		if let blur = self.view.viewWithTag(404){
		//			blur.removeFromSuperview()
		//		}
		self.loginViewCenterY.constant = self.view.frame.maxY
		
		UIView.animate(withDuration: 0.5,
		               delay: 0.0,
		               options: UIViewAnimationOptions.curveEaseInOut,
		               animations: {
						self.loginViewOpaque()
						self.loginView.layoutIfNeeded()
		},
		               
		               completion: { finished in
						self.loginView.isHidden = true
						if let blur = self.view.viewWithTag(404){
							blur.removeFromSuperview()
						}

						self.blurView?.removeGestureRecognizer(self.blurbackgroundTouched!)
							self.blurView = nil
						self.CUpassword.resignFirstResponder()
						self.CUusername.resignFirstResponder()
						
		})
		
	}
	override func viewWillDisappear(_ animated: Bool) {
		NotificationCenter.default.removeObserver(self, name: NSNotification.Name(rawValue: "reportKindSuccess") , object: nil)
		NotificationCenter.default.removeObserver(self, name: NSNotification.Name(rawValue: "involvementKindSuccess") , object: nil)
		NotificationCenter.default.removeObserver(self, name: NSNotification.Name.UIKeyboardWillShow, object: nil)
		NotificationCenter.default.removeObserver(self, name: NSNotification.Name.UIKeyboardWillHide, object: nil)

	}
	override func shouldPerformSegue(withIdentifier identifier: String, sender: Any?) -> Bool {
		if identifier == "reportView"
		{
			if let cookies = HTTPCookieStorage.shared.cookies {
				let shibCookie = cookies.filter { (cookie: HTTPCookie) in
					cookie.name.range(of: "shibsession") != nil
				}
				print(shibCookie)
				if(shibCookie == []){
					authenticate()
					// the cookie does not exist allow user to authenticate
					return false
				}
			else if(preloadedData.reportKinds.count == 0 || preloadedData.involvementKinds.count == 0)
			{
				self.alert = UIAlertController(title: "Please wait...", message: "\n\n\n\nData connection required.\n Please connect to a stronger connection before trying again.", preferredStyle: .alert)
				//self.loadingIndicator = UIActivityIndicatorView(frame: CGRectMake(50, 10, 37, 37))
				self.loadingIndicator.frame = CGRect(x: 110.5, y: 50, width: 50, height: 50)
				self.loadingIndicator.activityIndicatorViewStyle = .whiteLarge
				self.loadingIndicator.color = UIColor.black
				//  loadingIndicator.center = view.center;
				self.loadingIndicator.hidesWhenStopped = true
				self.loadingIndicator.startAnimating()
				self.alert?.view.addSubview(self.loadingIndicator)
				print("Accessing php scripts")
				//setReportView()
				presentedNoData = true
				preload()
				self.alert?.addAction(UIAlertAction(title: "Ok", style: .default, handler: {
					action in
					
					
					self.dismiss(animated: true, completion: nil)
				}))
				self.present(self.alert!, animated: true, completion: nil)
				return false
			}
			
		}
	}
		return true
	}
	
	func displayConnectionError(){
		self.alert = UIAlertController(title: "Data Connection Required", message: "You must be connected to a network in order to use this application.", preferredStyle: .alert)
		//self.loadingIndicator = UIActivityIndicatorView(frame: CGRectMake(50, 10, 37, 37))
			self.alert?.addAction(UIAlertAction(title: "Ok", style: .default, handler: {
			action in
			
			
			self.dismiss(animated: true, completion: nil)
		}))
		self.present(self.alert!, animated: true, completion: nil)
	}
	func aboutButtonHeld(holdGesture: UILongPressGestureRecognizer)
	{
		switch holdGesture.state
		{
		case .began:
			print("Began True")
			let appDelegate = UIApplication.shared.delegate as! AppDelegate
			appDelegate.animateViewIn()
		case .changed: break
		// do nothing
			
		case .ended:
			break

		case .failed:
			print("Unrecognized")
		default:
			print("Hold gesture default")
		}
	}

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
	
    override func viewDidAppear(_ animated: Bool) {
        super.viewDidAppear(animated)
		reportNiceCatch.titleLabel!.text = "Report a Nice Catch"
        if MyVariables.isSubmitted {
            //clearData()
            //preload()
            //presentThankYou()
            MyVariables.isSubmitted = false
        }
		self.loginViewOpaque()
//		self.loginView.center = CGPoint(x: view.frame.size.width/2, y: view.frame.size.height)
		self.CUusername.resignFirstResponder()
		self.CUpassword.resignFirstResponder()
		self.loginViewCenterYValue = self.loginViewCenterY.constant
		self.loginViewCenterY.constant = self.view.frame.maxY
		self.loginView.layoutIfNeeded()
    }

    func presentThankYou() {
        let alert = UIAlertController(title: "Thank You!", message: "Thank you for submitting a Nice Catch! report. The Research Safety office will review your report.", preferredStyle: UIAlertControllerStyle.alert)
        alert.addAction(UIAlertAction(title: "Ok", style: UIAlertActionStyle.default, handler: nil))
        self.present(alert, animated: true, completion: nil)
    }

    //BUG REPORTING - This link brings you to a google form to fill feedback about the app / ease of use
    @IBAction func didTapGoogle(sender: AnyObject) {
        UIApplication.shared.openURL(NSURL(string: "https://people.cs.clemson.edu/~jacosta/feedback.php")! as URL)
    }
    
}

