//
//  AppDelegate.swift
//  NiceCatch
//
//  Created by Joey Costa on 11/8/15.
//  Copyright © 2015 Joey Costa. All rights reserved.

import UIKit
import CoreData

extension UIColor {
	convenience init(red: Int, green: Int, blue: Int) {
		assert(red >= 0 && red <= 255, "Invalid red component")
		assert(green >= 0 && green <= 255, "Invalid green component")
		assert(blue >= 0 && blue <= 255, "Invalid blue component")
		
		self.init(red: CGFloat(red) / 255.0, green: CGFloat(green) / 255.0, blue: CGFloat(blue) / 255.0, alpha: 1.0)
	}
	
	convenience init(netHex:Int) {
		self.init(red:(netHex >> 16) & 0xff, green:(netHex >> 8) & 0xff, blue:netHex & 0xff)
	}
}

extension UIImage {
	// create image of solid color
	class func imageWithColor(color: UIColor, size: CGSize) -> UIImage {
		UIGraphicsBeginImageContextWithOptions(size, true, 0.0)
		let ctx = UIGraphicsGetCurrentContext()
		color.setFill()
		ctx!.fill(CGRect(origin: .zero, size: size))
		let image = UIGraphicsGetImageFromCurrentImageContext()
		UIGraphicsEndImageContext()
		return image!
	}
}
@UIApplicationMain
class AppDelegate: UIResponder, UIApplicationDelegate, CAAnimationDelegate {

    var window: UIWindow?

	var reach: Reach = Reach .forInternetConnection()
	func reachabilityChanged(notification: NSNotification) {
		if self.reach.currentReachabilityStatus().rawValue == ReachableViaWiFi.rawValue || self.reach.currentReachabilityStatus().rawValue == ReachableViaWWAN.rawValue {
			preload()
			print("Service avalaible!!!")
		} else {
			print("No service avalaible!!!")
		}
	}
	func application(_ application: UIApplication, didFinishLaunchingWithOptions launchOptions: [UIApplicationLaunchOptionsKey : Any]? = nil) -> Bool {
		// Override point for customization after application launch.
		// Look for network changes
		// Allocate a reachability object
		//self.reach = Reachability.forInternetConnection()
		
		// Tell the reachability that we DON'T want to be reachable on 3G/EDGE/CDMA
		//self.reach!.reachableOnWWAN = false
		
		
		// Here we set up a NSNotification observer. The Reachability that caused the notification
		// is passed in the object parameter
		NotificationCenter.default.addObserver(self,
		                                       selector: #selector(AppDelegate.reachabilityChanged(notification:)),
		                                       name: NSNotification.Name.reachabilityChanged,
		                                       object: nil)
		
		self.reach.startNotifier()
		
		
		let barColor = UIColor(netHex: 0x522D80)
		//let shadowColor = UIColor(red: 0/255, green: 114/255, blue: 30/255, alpha: 1.0)
		
		let navBarFont = UIFont.systemFont(ofSize: 17.0)
		
		window?.tintColor = barColor
		
		// Navigation Bar
		let navBarAppearance = UINavigationBar.appearance()
		navBarAppearance.isTranslucent = true
		navBarAppearance.titleTextAttributes = [NSForegroundColorAttributeName : UIColor.white,
		                                        NSFontAttributeName : navBarFont]
		
		let imageSize = CGSize(width: 1, height: 1)
		let backgroundImage = UIImage.imageWithColor(color: barColor, size: imageSize)
		navBarAppearance.setBackgroundImage(backgroundImage, for: .default)
		let shadowImage = UIImage.imageWithColor(color: barColor, size: imageSize)
		navBarAppearance.shadowImage = shadowImage
		navBarAppearance.tintColor = UIColor.white
		navBarAppearance.isTranslucent = false
		
		// Tab Bar
		UITabBar.appearance().tintColor = barColor
		UITabBarItem.appearance().setTitleTextAttributes([ NSForegroundColorAttributeName: barColor ], for: UIControlState.normal)
		UITabBarItem.appearance().setTitleTextAttributes([ NSForegroundColorAttributeName: barColor ], for: UIControlState.selected)
		
		self.animateViewIn()
		
		
		return true

	}
	
func animateViewIn()
{
	// Animated Opening - https://github.com/okmr-d/App-Launching-like-Twitter
	
	self.window = UIWindow(frame: UIScreen.main.bounds)
	//self.window!.backgroundColor = UIColor(netHex: 0xF2693A) // Clemson Orange
	self.window!.backgroundColor = UIColor(netHex: 0x522D80) // Clemson Purple
	self.window!.makeKeyAndVisible()
	
	// rootViewController from StoryBoard
	let mainStoryboard: UIStoryboard = UIStoryboard(name: "Main", bundle: nil)
	let navigationController = mainStoryboard.instantiateViewController(withIdentifier: "navigationController")
	self.window!.rootViewController = navigationController
	
	// logo mask
	navigationController.view.layer.mask = CALayer()
	navigationController.view.layer.mask!.contents = UIImage(named: "TigerPaw", in: Bundle.main, compatibleWith: .none)?.cgImage
	navigationController.view.layer.mask!.bounds = CGRect(x: 0, y: 0, width: 100, height: 100)
//	navigationController.view.layer.mask!.anchorPoint = CGPoint(x: 0.5, y: 0.5)
	navigationController.view.layer.mask!.position = CGPoint(x: navigationController.view.frame.width / 2, y: navigationController.view.frame.height / 2)
	// color navigation bar
	
	// logo mask background view
	let maskBgView = UIView(frame: navigationController.view.frame)
	maskBgView.backgroundColor = UIColor.white
	navigationController.view.addSubview(maskBgView)
	navigationController.view.bringSubview(toFront: maskBgView)
	
	// logo mask animation
	let transformAnimation = CAKeyframeAnimation(keyPath: "bounds")
	transformAnimation.delegate = self
	transformAnimation.duration = 1
	transformAnimation.beginTime = CACurrentMediaTime() + 1 //add delay of 1 second
	let initalBounds = NSValue(cgRect: navigationController.view.layer.mask!.bounds)
	let secondBounds = NSValue(cgRect: CGRect(x: 0, y: 0, width: 50, height: 50))
	//let finalBounds = NSValue(CGRect: CGRect(x: 0, y: 0, width: UIScreen.mainScreen().bounds.width, height: UIScreen.mainScreen().bounds.width))
	let finalBounds = NSValue(cgRect: CGRect(x: 0, y: 0, width: 1500, height: 1500))
	transformAnimation.values = [initalBounds, secondBounds, finalBounds]
	transformAnimation.keyTimes = [0, 0.5, 1]
	transformAnimation.timingFunctions = [CAMediaTimingFunction(name: kCAMediaTimingFunctionEaseInEaseOut), CAMediaTimingFunction(name: kCAMediaTimingFunctionEaseOut)]
	transformAnimation.isRemovedOnCompletion = false
	transformAnimation.fillMode = kCAFillModeForwards
	navigationController.view.layer.mask!.add(transformAnimation, forKey: "maskAnimation")
	
	// logo mask background view animation
	UIView.animate(withDuration: 0.25,
	                           delay: 1.3,
	                           options: UIViewAnimationOptions.curveEaseIn,
	                           animations: {
								maskBgView.alpha = 0.0
		},
	                           completion: { finished in
								maskBgView.removeFromSuperview()
	})
	
	// root view animation
	UIView.animate(withDuration: 0.25,
	                           delay: 1.3,
	                           options: [],
	                           animations: {
								self.window!.rootViewController!.view.transform = CGAffineTransform(scaleX: 1.1, y: 1.1)
		},
	                           completion: { finished in
								UIView.animate(withDuration: 0.3,
									delay: 0.0,
									options: UIViewAnimationOptions.curveEaseInOut,
									animations: {
										self.window!.rootViewController!.view.transform = CGAffineTransform.identity
									},
									completion: nil
								)
	})
	

	}
    func applicationWillResignActive(_ application: UIApplication) {
        // Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
        // Use this method to pause ongoing tasks, disable timers, and throttle down OpenGL ES frame rates. Games should use this method to pause the game.
    }

    func applicationDidEnterBackground(_ application: UIApplication) {
        // Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later.
        // If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
		self.window?.endEditing(true)
    }

    func applicationWillEnterForeground(_ application: UIApplication) {
        // Called as part of the transition from the background to the inactive state; here you can undo many of the changes made on entering the background.
    }

    func applicationDidBecomeActive(_ application: UIApplication) {
        // Restart any tasks that were paused (or not yet started) while the application was inactive. If the application was previously in the background, optionally refresh the user interface.
    }

    func applicationWillTerminate(_ application: UIApplication) {
        // Called when the application is about to terminate. Save data if appropriate. See also applicationDidEnterBackground:.
        // Saves changes in the application's managed object context before the application terminates.
		// self.saveContext()
    }
	func animationDidStop(_ anim: CAAnimation, finished flag: Bool) {
		// remove mask when animation completes
		self.window!.rootViewController!.view.layer.mask = nil
	}


}

