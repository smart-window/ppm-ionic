var input = "Emoji search...",
msEmo = [
{
	"name": "People",
	"emojies": [
	{
		"emj": "๐",
		"title": "Grinning Face"
	},
	{
		"emj": "๐",
		"title": "Beaming Face With Smiling Eyes"
	},
	{
		"emj": "๐",
		"title": "Face With Tears Of Joy"
	},
	{
		"emj": "๐คฃ",
		"title": "Rolling On The Floor Laughing"
	},
	{
		"emj": "๐",
		"title": "Grinning Face With Big Eyes"
	},
	{
		"emj": "๐",
		"title": "Grinning Face With Smiling Eyes"
	},
	{
		"emj": "๐",
		"title": "Grinning Face With Sweat"
	},
	{
		"emj": "๐",
		"title": "Grinning Squinting Face"
	},
	{
		"emj": "๐",
		"title": "Winking Face"
	},
	{
		"emj": "๐",
		"title": "Smiling Face With Smiling Eyes"
	},
	{
		"emj": "๐",
		"title": "Face Savoring Food"
	},
	{
		"emj": "๐",
		"title": "Smiling Face With Sunglasses"
	},
	{
		"emj": "๐",
		"title": "Smiling Face With Heart-eyes"
	},
	{
		"emj": "๐",
		"title": "Face Blowing A Kiss"
	},
	{
		"emj": "๐",
		"title": "Kissing Face"
	},
	{
		"emj": "๐",
		"title": "Kissing Face With Smiling Eyes"
	},
	{
		"emj": "๐",
		"title": "Kissing Face With Closed Eyes"
	},
	{
		"emj": "๐",
		"title": "Slightly Smiling Face"
	},
	{
		"emj": "๐ค",
		"title": "Hugging Face"
	},
	{
		"emj": "๐คฉ",
		"title": "Star-struck"
	},
	{
		"emj": "๐ค",
		"title": "Thinking Face"
	},
	{
		"emj": "๐คจ",
		"title": "Face With Raised Eyebrow"
	},
	{
		"emj": "๐",
		"title": "Neutral Face"
	},
	{
		"emj": "๐",
		"title": "Expressionless Face"
	},
	{
		"emj": "๐ถ",
		"title": "Face Without Mouth"
	},
	{
		"emj": "๐",
		"title": "Face With Rolling Eyes"
	},
	{
		"emj": "๐",
		"title": "Smirking Face"
	},
	{
		"emj": "๐ฃ",
		"title": "Persevering Face"
	},
	{
		"emj": "๐ฅ",
		"title": "Sad But Relieved Face"
	},
	{
		"emj": "๐ฎ",
		"title": "Face With Open Mouth"
	},
	{
		"emj": "๐ค",
		"title": "Zipper-mouth Face"
	},
	{
		"emj": "๐ฏ",
		"title": "Hushed Face"
	},
	{
		"emj": "๐ช",
		"title": "Sleepy Face"
	},
	{
		"emj": "๐ซ",
		"title": "Tired Face"
	},
	{
		"emj": "๐ด",
		"title": "Sleeping Face"
	},
	{
		"emj": "๐",
		"title": "Relieved Face"
	},
	{
		"emj": "๐",
		"title": "Face With Tongue"
	},
	{
		"emj": "๐",
		"title": "Winking Face With Tongue"
	},
	{
		"emj": "๐",
		"title": "Squinting Face With Tongue"
	},
	{
		"emj": "๐คค",
		"title": "Drooling Face"
	},
	{
		"emj": "๐",
		"title": "Unamused Face"
	},
	{
		"emj": "๐",
		"title": "Downcast Face With Sweat"
	},
	{
		"emj": "๐",
		"title": "Pensive Face"
	},
	{
		"emj": "๐",
		"title": "Confused Face"
	},
	{
		"emj": "๐",
		"title": "Upside-down Face"
	},
	{
		"emj": "๐ค",
		"title": "Money-mouth Face"
	},
	{
		"emj": "๐ฒ",
		"title": "Astonished Face"
	},
	{
		"emj": "๐",
		"title": "Slightly Frowning Face"
	},
	{
		"emj": "๐",
		"title": "Confounded Face"
	},
	{
		"emj": "๐",
		"title": "Disappointed Face"
	},
	{
		"emj": "๐",
		"title": "Worried Face"
	},
	{
		"emj": "๐ค",
		"title": "Face With Steam From Nose"
	},
	{
		"emj": "๐ข",
		"title": "Crying Face"
	},
	{
		"emj": "๐ญ",
		"title": "Loudly Crying Face"
	},
	{
		"emj": "๐ฆ",
		"title": "Frowning Face With Open Mouth"
	},
	{
		"emj": "๐ง",
		"title": "Anguished Face"
	},
	{
		"emj": "๐จ",
		"title": "Fearful Face"
	},
	{
		"emj": "๐ฉ",
		"title": "Weary Face"
	},
	{
		"emj": "๐คฏ",
		"title": "Exploding Head"
	},
	{
		"emj": "๐ฌ",
		"title": "Grimacing Face"
	},
	{
		"emj": "๐ฐ",
		"title": "Anxious Face With Sweat"
	},
	{
		"emj": "๐ฑ",
		"title": "Face Screaming In Fear"
	},
	{
		"emj": "๐ณ",
		"title": "Flushed Face"
	},
	{
		"emj": "๐คช",
		"title": "Zany Face"
	},
	{
		"emj": "๐ต",
		"title": "Dizzy Face"
	},
	{
		"emj": "๐ก",
		"title": "Pouting Face"
	},
	{
		"emj": "๐?",
		"title": "Angry Face"
	},
	{
		"emj": "๐คฌ",
		"title": "Face With Symbols On Mouth"
	},
	{
		"emj": "๐ท",
		"title": "Face With Medical Mask"
	},
	{
		"emj": "๐ค",
		"title": "Face With Thermometer"
	},
	{
		"emj": "๐ค",
		"title": "Face With Head-bandage"
	},
	{
		"emj": "๐คข",
		"title": "Nauseated Face"
	},
	{
		"emj": "๐คฎ",
		"title": "Face Vomiting"
	},
	{
		"emj": "๐คง",
		"title": "Sneezing Face"
	},
	{
		"emj": "๐",
		"title": "Smiling Face With Halo"
	},
	{
		"emj": "๐ค?",
		"title": "Cowboy Hat Face"
	},
	{
		"emj": "๐คฅ",
		"title": "Lying Face"
	},
	{
		"emj": "๐คซ",
		"title": "Shushing Face"
	},
	{
		"emj": "๐คญ",
		"title": "Face With Hand Over Mouth"
	},
	{
		"emj": "๐ง",
		"title": "Face With Monocle"
	},
	{
		"emj": "๐ค",
		"title": "Nerd Face"
	},
	{
		"emj": "๐",
		"title": "Smiling Face With Horns"
	},
	{
		"emj": "๐ฟ",
		"title": "Angry Face With Horns"
	},
	{
		"emj": "๐คก",
		"title": "Clown Face"
	},
	{
		"emj": "๐น",
		"title": "Ogre"
	},
	{
		"emj": "๐บ",
		"title": "Goblin"
	},
	{
		"emj": "๐",
		"title": "Skull"
	},
	{
		"emj": "โ?",
		"title": "Skull And Crossbones"
	},
	{
		"emj": "๐ป",
		"title": "Ghost"
	},
	{
		"emj": "๐ฝ",
		"title": "Alien"
	},
	{
		"emj": "๐พ",
		"title": "Alien Monster"
	},
	{
		"emj": "๐ค",
		"title": "Robot Face"
	},
	{
		"emj": "๐ฉ",
		"title": "Pile Of Poo"
	},
	{
		"emj": "๐บ",
		"title": "Grinning Cat Face"
	},
	{
		"emj": "๐ธ",
		"title": "Grinning Cat Face With Smiling Eyes"
	},
	{
		"emj": "๐น",
		"title": "Cat Face With Tears Of Joy"
	},
	{
		"emj": "๐ป",
		"title": "Smiling Cat Face With Heart-eyes"
	},
	{
		"emj": "๐ผ",
		"title": "Cat Face With Wry Smile"
	},
	{
		"emj": "๐ฝ",
		"title": "Kissing Cat Face"
	},
	{
		"emj": "๐",
		"title": "Weary Cat Face"
	},
	{
		"emj": "๐ฟ",
		"title": "Crying Cat Face"
	},
	{
		"emj": "๐พ",
		"title": "Pouting Cat Face"
	},
	{
		"emj": "๐",
		"title": "See-no-evil Monkey"
	},
	{
		"emj": "๐",
		"title": "Hear-no-evil Monkey"
	},
	{
		"emj": "๐",
		"title": "Speak-no-evil Monkey"
	},
	{
		"emj": "๐ถ",
		"title": "Baby"
	},
	{
		"emj": "๐ง",
		"title": "Child"
	},
	{
		"emj": "๐ฆ",
		"title": "Boy"
	},
	{
		"emj": "๐ง",
		"title": "Girl"
	},
	{
		"emj": "๐ง",
		"title": "Adult"
	},
	{
		"emj": "๐จ",
		"title": "Man"
	},
	{
		"emj": "๐ฉ",
		"title": "Woman"
	},
	{
		"emj": "๐ง",
		"title": "Older Adult"
	},
	{
		"emj": "๐ด",
		"title": "Old Man"
	},
	{
		"emj": "๐ต",
		"title": "Old Woman"
	},
	{
		"emj": "๐จโโ๏ธ",
		"title": "Man Health Worker"
	},
	{
		"emj": "๐ฉโโ๏ธ",
		"title": "Woman Health Worker"
	},
	{
		"emj": "๐จโ๐",
		"title": "Man Student"
	},
	{
		"emj": "๐ฉโ๐",
		"title": "Woman Student"
	},
	{
		"emj": "๐จโ๐ซ",
		"title": "Man Teacher"
	},
	{
		"emj": "๐ฉโ๐ซ",
		"title": "Woman Teacher"
	},
	{
		"emj": "๐จโโ๏ธ",
		"title": "Man Judge"
	},
	{
		"emj": "๐ฉโโ๏ธ",
		"title": "Woman Judge"
	},
	{
		"emj": "๐จโ๐พ",
		"title": "Man Farmer"
	},
	{
		"emj": "๐ฉโ๐พ",
		"title": "Woman Farmer"
	},
	{
		"emj": "๐จโ๐ณ",
		"title": "Man Cook"
	},
	{
		"emj": "๐ฉโ๐ณ",
		"title": "Woman Cook"
	},
	{
		"emj": "๐จโ๐ง",
		"title": "Man Mechanic"
	},
	{
		"emj": "๐ฉโ๐ง",
		"title": "Woman Mechanic"
	},
	{
		"emj": "๐จโ๐ญ",
		"title": "Man Factory Worker"
	},
	{
		"emj": "๐ฉโ๐ญ",
		"title": "Woman Factory Worker"
	},
	{
		"emj": "๐จโ๐ผ",
		"title": "Man Office Worker"
	},
	{
		"emj": "๐ฉโ๐ผ",
		"title": "Woman Office Worker"
	},
	{
		"emj": "๐จโ๐ฌ",
		"title": "Man Scientist"
	},
	{
		"emj": "๐ฉโ๐ฌ",
		"title": "Woman Scientist"
	},
	{
		"emj": "๐จโ๐ป",
		"title": "Man Technologist"
	},
	{
		"emj": "๐ฉโ๐ป",
		"title": "Woman Technologist"
	},
	{
		"emj": "๐จโ๐ค",
		"title": "Man Singer"
	},
	{
		"emj": "๐ฉโ๐ค",
		"title": "Woman Singer"
	},
	{
		"emj": "๐จโ๐จ",
		"title": "Man Artist"
	},
	{
		"emj": "๐ฉโ๐จ",
		"title": "Woman Artist"
	},
	{
		"emj": "๐จโโ๏ธ",
		"title": "Man Pilot"
	},
	{
		"emj": "๐ฉโโ๏ธ",
		"title": "Woman Pilot"
	},
	{
		"emj": "๐จโ๐",
		"title": "Man Astronaut"
	},
	{
		"emj": "๐ฉโ๐",
		"title": "Woman Astronaut"
	},
	{
		"emj": "๐จโ๐",
		"title": "Man Firefighter"
	},
	{
		"emj": "๐ฉโ๐",
		"title": "Woman Firefighter"
	},
	{
		"emj": "๐ฎโโ๏ธ",
		"title": "Man Police Officer"
	},
	{
		"emj": "๐ฎโโ๏ธโ",
		"title": "Woman Police Officer"
	},
	{
		"emj": "๐ต๏ธโโ๏ธ",
		"title": "Man Detective"
	},
	{
		"emj": "๐ต๏ธโโ๏ธ",
		"title": "Woman Detective"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Man Guard"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Woman Guard"
	},
	{
		"emj": "๐ทโโ๏ธ",
		"title": "Man Construction Worker"
	},
	{
		"emj": "๐ทโโ๏ธ",
		"title": "Woman Construction Worker"
	},
	{
		"emj": "๐คด",
		"title": "Prince"
	},
	{
		"emj": "๐ธ",
		"title": "Princess"
	},
	{
		"emj": "๐ณโโ๏ธ",
		"title": "Man Wearing Turban"
	},
	{
		"emj": "๐ณโโ๏ธ",
		"title": "Woman Wearing Turban"
	},
	{
		"emj": "๐ฒ",
		"title": "Man With Chinese Cap"
	},
	{
		"emj": "๐ง",
		"title": "Woman With Headscarf"
	},
	{
		"emj": "๐ง",
		"title": "Bearded Person"
	},
	{
		"emj": "๐ฑโโ๏ธ",
		"title": "Blond-haired Man"
	},
	{
		"emj": "๐ฑโโ๏ธ",
		"title": "Blond-haired Woman"
	},
	{
		"emj": "๐คต",
		"title": "Man In Tuxedo"
	},
	{
		"emj": "๐ฐ",
		"title": "Bride With Veil"
	},
	{
		"emj": "๐คฐ",
		"title": "Pregnant Woman"
	},
	{
		"emj": "๐คฑ",
		"title": "Breast-feeding"
	},
	{
		"emj": "๐ผ",
		"title": "Baby Angel"
	},
	{
		"emj": "๐",
		"title": "Santa Claus"
	},
	{
		"emj": "๐คถ",
		"title": "Mrs. Claus"
	},
	{
		"emj": "๐งโโ๏ธ",
		"title": "Woman Mage"
	},
	{
		"emj": "๐งโโ๏ธ",
		"title": "Man Mage"
	},
	{
		"emj": "๐งโโ๏ธ",
		"title": "Woman Fairy"
	},
	{
		"emj": "๐งโโ๏ธ",
		"title": "Man Fairy"
	},
	{
		"emj": "๐งโโ๏ธ",
		"title": "Woman Vampire"
	},
	{
		"emj": "๐งโโ๏ธ",
		"title": "Man Vampire"
	},
	{
		"emj": "๐งโโ๏ธ",
		"title": "Mermaid"
	},
	{
		"emj": "๐งโโ๏ธ",
		"title": "Merman"
	},
	{
		"emj": "๐งโโ๏ธ",
		"title": "Woman Elf"
	},
	{
		"emj": "๐งโโ๏ธ",
		"title": "Man Elf"
	},
	{
		"emj": "๐งโโ๏ธ",
		"title": "Woman Genie"
	},
	{
		"emj": "๐งโโ๏ธ",
		"title": "Man Genie"
	},
	{
		"emj": "๐งโโ๏ธ",
		"title": "Woman Zombie"
	},
	{
		"emj": "๐งโโ๏ธ",
		"title": "Man Zombie"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Man Frowning"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Woman Frowning"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Man Pouting"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Woman Pouting"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Man Gesturing NO"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Woman Gesturing NO"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Man Gesturing OK"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Woman Gesturing OK"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Man Tipping Hand"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Woman Tipping Hand"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Man Raising Hand"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Woman Raising Hand"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Man Bowing"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Woman Bowing"
	},
	{
		"emj": "๐คฆโโ๏ธ",
		"title": "Man Facepalming"
	},
	{
		"emj": "๐คฆโโ๏ธ",
		"title": "Woman Facepalming"
	},
	{
		"emj": "๐คทโโ๏ธ",
		"title": "Man Shrugging"
	},
	{
		"emj": "๐คทโโ๏ธ",
		"title": "Woman Shrugging"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Man Getting Massage"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Woman Getting Massage"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Man Getting Haircut"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Woman Getting Haircut"
	},
	{
		"emj": "๐ถโโ๏ธ",
		"title": "Man Walking"
	},
	{
		"emj": "๐ถโโ๏ธ",
		"title": "Woman Walking"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Man Running"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Woman Running"
	},
	{
		"emj": "๐",
		"title": "Woman Dancing"
	},
	{
		"emj": "๐บ",
		"title": "Man Dancing"
	},
	{
		"emj": "๐ฏ",
		"title": "People With Bunny Ears"
	},
	{
		"emj": "๐ฏโโ๏ธ",
		"title": "Men With Bunny Ears"
	},
	{
		"emj": "๐ฏโโ๏ธ",
		"title": "Women With Bunny Ears"
	},
	{
		"emj": "๐งโโ๏ธ",
		"title": "Woman In Steamy Room"
	},
	{
		"emj": "๐งโโ๏ธ",
		"title": "Man In Steamy Room"
	},
	{
		"emj": "๐งโโ๏ธ",
		"title": "Woman Climbing"
	},
	{
		"emj": "๐งโโ๏ธ",
		"title": "Man Climbing"
	},
	{
		"emj": "๐งโโ๏ธ",
		"title": "Woman In Lotus Position"
	},
	{
		"emj": "๐งโโ๏ธ",
		"title": "Man In Lotus Position"
	},
	{
		"emj": "๐",
		"title": "Person Taking Bath"
	},
	{
		"emj": "๐",
		"title": "Person In Bed"
	},
	{
		"emj": "๐ด",
		"title": "Man In Suit Levitating"
	},
	{
		"emj": "๐ฃ",
		"title": "Speaking Head"
	},
	{
		"emj": "๐ค",
		"title": "Bust In Silhouette"
	},
	{
		"emj": "๐ฅ",
		"title": "Busts In Silhouette"
	},
	{
		"emj": "๐คบ",
		"title": "Person Fencing"
	},
	{
		"emj": "๐",
		"title": "Horse Racing"
	},
	{
		"emj": "โท",
		"title": "Skier"
	},
	{
		"emj": "๐",
		"title": "Snowboarder"
	},
	{
		"emj": "๐๏ธโโ๏ธ",
		"title": "Man Golfing"
	},
	{
		"emj": "๐๏ธโโ๏ธ",
		"title": "Woman Golfing"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Man Surfing"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Woman Surfing"
	},
	{
		"emj": "๐ฃโโ๏ธ",
		"title": "Man Rowing Boat"
	},
	{
		"emj": "๐ฃโโ๏ธ",
		"title": "Woman Rowing Boat"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Man Swimming"
	},
	{
		"emj": "๐โโ๏ธ",
		"title": "Woman Swimming"
	},
	{
		"emj": "โน๏ธโโ๏ธ",
		"title": "Man Bouncing Ball"
	},
	{
		"emj": "โน๏ธโโ๏ธ",
		"title": "Woman Bouncing Ball"
	},
	{
		"emj": "๐๏ธโโ๏ธ",
		"title": "Man Lifting Weights"
	},
	{
		"emj": "๐๏ธโโ๏ธ",
		"title": "Woman Lifting Weights"
	},
	{
		"emj": "๐ดโโ๏ธ",
		"title": "Man Biking"
	},
	{
		"emj": "๐ดโโ๏ธ",
		"title": "Woman Biking"
	},
	{
		"emj": "๐ตโโ๏ธ",
		"title": "Man Mountain Biking"
	},
	{
		"emj": "๐ตโโ๏ธ",
		"title": "Woman Mountain Biking"
	},
	{
		"emj": "๐",
		"title": "Racing Car"
	},
	{
		"emj": "๐",
		"title": "Motorcycle"
	},
	{
		"emj": "๐คธโโ๏ธ",
		"title": "Man Cartwheeling"
	},
	{
		"emj": "๐คธโโ๏ธ",
		"title": "Woman Cartwheeling"
	},
	{
		"emj": "๐คผ",
		"title": "People Wrestling"
	},
	{
		"emj": "๐คผโโ๏ธ",
		"title": "Men Wrestling"
	},
	{
		"emj": "๐คผโโ๏ธ",
		"title": "Women Wrestling"
	},
	{
		"emj": "๐คฝโโ๏ธ",
		"title": "Man Playing Water Polo"
	},
	{
		"emj": "๐คฝโโ๏ธ",
		"title": "Woman Playing Water Polo"
	},
	{
		"emj": "๐คพโโ๏ธ",
		"title": "Man Playing Handball"
	},
	{
		"emj": "๐คพโโ๏ธ",
		"title": "Woman Playing Handball"
	},
	{
		"emj": "๐คนโโ๏ธ",
		"title": "Man Juggling"
	},
	{
		"emj": "๐คนโโ๏ธ",
		"title": "Woman Juggling"
	},
	{
		"emj": "๐ซ",
		"title": "Man And Woman Holding Hands"
	},
	{
		"emj": "๐ฌ",
		"title": "Two Men Holding Hands"
	},
	{
		"emj": "๐ญ",
		"title": "Two Women Holding Hands"
	},
	{
		"emj": "๐",
		"title": "Kiss"
	},
	{
		"emj": "๐ฉโโค๏ธโ๐โ๐จ",
		"title": "Woman Kiss Man"
	},
	{
		"emj": "๐จโโค๏ธโ๐โ๐จ",
		"title": "Man Kiss Man"
	},
	{
		"emj": "๐ฉโโค๏ธโ๐โ๐ฉ",
		"title": "Woman Kiss Woman"
	},
	{
		"emj": "๐",
		"title": "Couple With Heart"
	},
	{
		"emj": "๐ฉโโค๏ธโ๐จ",
		"title": "Couple With Heart: Woman Man"
	},
	{
		"emj": "๐จโโค๏ธโ๐จ",
		"title": "Couple With Heart: Man Man"
	},
	{
		"emj": "๐ฉโโค๏ธโ๐ฉ",
		"title": "Couple With Heart: Woman Woman"
	},
	{
		"emj": "๐ช",
		"title": "Family"
	},
	{
		"emj": "๐จโ๐ฉโ๐ฆ",
		"title": "Family: Man Woman Boy"
	},
	{
		"emj": "๐จโ๐ฉโ๐ง",
		"title": "Family: Man Woman Girl"
	},
	{
		"emj": "๐จโ๐ฉโ๐งโ๐ฆ",
		"title": "Family: Man Woman Girl Boy"
	},
	{
		"emj": "๐จโ๐ฉโ๐ฆโ๐ฆ",
		"title": "Family: Man Woman Boy Boy"
	},
	{
		"emj": "๐จโ๐ฉโ๐งโ๐ง",
		"title": "Family: Man Woman Girl Girl"
	},
	{
		"emj": "๐จโ๐จโ๐ฆ",
		"title": "Family: Man Man Boy"
	},
	{
		"emj": "๐จโ๐จโ๐ง",
		"title": "Family: Man Man Girl"
	},
	{
		"emj": "๐จโ๐จโ๐งโ๐ฆ",
		"title": "Family: Man Man Girl Boy"
	},
	{
		"emj": "๐จโ๐จโ๐ฆโ๐ฆ",
		"title": "Family: Man Man Boy Boy"
	},
	{
		"emj": "๐จโ๐จโ๐งโ๐ง",
		"title": "Family: Man Man Girl Girl"
	},
	{
		"emj": "๐ฉโ๐ฉโ๐ฆ",
		"title": "Family: Woman Woman Boy"
	},
	{
		"emj": "๐ฉโ๐ฉโ๐ง",
		"title": "Family: Woman Woman Girl"
	},
	{
		"emj": "๐ฉโ๐ฉโ๐งโ๐ฆ",
		"title": "Family: Woman Woman Girl Boy"
	},
	{
		"emj": "๐ฉโ๐ฉโ๐ฆโ๐ฆ",
		"title": "Family: Woman Woman Boy Boy"
	},
	{
		"emj": "๐ฉโ๐ฉโ๐งโ๐ง",
		"title": "Family: Woman Woman Girl Girl"
	},
	{
		"emj": "๐จโ๐ฆ",
		"title": "Family: Man Boy"
	},
	{
		"emj": "๐จโ๐ฆโ๐ฆ",
		"title": "Family: Man Boy Boy"
	},
	{
		"emj": "๐จโ๐ง",
		"title": "Family: Man Girl"
	},
	{
		"emj": "๐จโ๐งโ๐ฆ",
		"title": "Family: Man Girl Boy"
	},
	{
		"emj": "๐จโ๐งโ๐ง",
		"title": "Family: Man Girl Girl"
	},
	{
		"emj": "๐ฉโ๐ฆ",
		"title": "Family: Woman Boy"
	},
	{
		"emj": "๐ฉโ๐ฆโ๐ฆ",
		"title": "Family: Woman Boy Boy"
	},
	{
		"emj": "๐ฉโ๐ง",
		"title": "Family: Woman Girl"
	},
	{
		"emj": "๐ฉโ๐งโ๐ฆ",
		"title": "Family: Woman Girl Boy"
	},
	{
		"emj": "๐ฉโ๐งโ๐ง",
		"title": "Family: Woman Girl Girl"
	},
	{
		"emj": "๐คณ",
		"title": "Selfie"
	},
	{
		"emj": "๐ช",
		"title": "Flexed Biceps"
	},
	{
		"emj": "๐",
		"title": "Backhand Index Pointing Left"
	},
	{
		"emj": "๐",
		"title": "Backhand Index Pointing Right"
	},
	{
		"emj": "โ",
		"title": "Index Pointing Up"
	},
	{
		"emj": "๐",
		"title": "Backhand Index Pointing Up"
	},
	{
		"emj": "๐",
		"title": "Middle Finger"
	},
	{
		"emj": "๐",
		"title": "Backhand Index Pointing Down"
	},
	{
		"emj": "โ",
		"title": "Victory Hand"
	},
	{
		"emj": "๐ค",
		"title": "Crossed Fingers"
	},
	{
		"emj": "๐",
		"title": "Vulcan Salute"
	},
	{
		"emj": "๐ค",
		"title": "Sign Of The Horns"
	},
	{
		"emj": "๐ค",
		"title": "Call Me Hand"
	},
	{
		"emj": "๐",
		"title": "Hand With Fingers Splayed"
	},
	{
		"emj": "โ",
		"title": "Raised Hand"
	},
	{
		"emj": "๐",
		"title": "OK Hand"
	},
	{
		"emj": "๐",
		"title": "Thumbs Up"
	},
	{
		"emj": "๐",
		"title": "Thumbs Down"
	},
	{
		"emj": "โ",
		"title": "Raised Fist"
	},
	{
		"emj": "๐",
		"title": "Oncoming Fist"
	},
	{
		"emj": "๐ค",
		"title": "Left-facing Fist"
	},
	{
		"emj": "๐ค",
		"title": "Right-facing Fist"
	},
	{
		"emj": "๐ค",
		"title": "Raised Back Of Hand"
	},
	{
		"emj": "๐",
		"title": "Waving Hand"
	},
	{
		"emj": "๐ค",
		"title": "Love-you Gesture"
	},
	{
		"emj": "โ",
		"title": "Writing Hand"
	},
	{
		"emj": "๐",
		"title": "Clapping Hands"
	},
	{
		"emj": "๐",
		"title": "Open Hands"
	},
	{
		"emj": "๐",
		"title": "Raising Hands"
	},
	{
		"emj": "๐คฒ",
		"title": "Palms Up Together"
	},
	{
		"emj": "๐",
		"title": "Folded Hands"
	},
	{
		"emj": "๐ค",
		"title": "Handshake"
	},
	{
		"emj": "๐",
		"title": "Nail Polish"
	},
	{
		"emj": "๐",
		"title": "Ear"
	},
	{
		"emj": "๐",
		"title": "Nose"
	},
	{
		"emj": "๐ฃ",
		"title": "Footprints"
	},
	{
		"emj": "๐",
		"title": "Eyes"
	},
	{
		"emj": "๐",
		"title": "Eye"
	},
	{
		"emj": "๐๏ธโ๐จ๏ธ",
		"title": "Eye In Speech Bubble"
	},
	{
		"emj": "๐ง?",
		"title": "Brain"
	},
	{
		"emj": "๐",
		"title": "Tongue"
	},
	{
		"emj": "๐",
		"title": "Mouth"
	},
	{
		"emj": "๐",
		"title": "Kiss Mark"
	},
	{
		"emj": "๐",
		"title": "Heart With Arrow"
	},
	{
		"emj": "โค",
		"title": "Red Heart"
	},
	{
		"emj": "๐",
		"title": "Beating Heart"
	},
	{
		"emj": "๐",
		"title": "Broken Heart"
	},
	{
		"emj": "๐",
		"title": "Two Hearts"
	},
	{
		"emj": "๐",
		"title": "Sparkling Heart"
	},
	{
		"emj": "๐",
		"title": "Growing Heart"
	},
	{
		"emj": "๐",
		"title": "Blue Heart"
	},
	{
		"emj": "๐",
		"title": "Green Heart"
	},
	{
		"emj": "๐",
		"title": "Yellow Heart"
	},
	{
		"emj": "๐งก",
		"title": "Orange Heart"
	},
	{
		"emj": "๐",
		"title": "Purple Heart"
	},
	{
		"emj": "๐ค",
		"title": "Black Heart"
	},
	{
		"emj": "๐",
		"title": "Heart With Ribbon"
	},
	{
		"emj": "๐",
		"title": "Revolving Hearts"
	},
	{
		"emj": "๐",
		"title": "Heart Decoration"
	},
	{
		"emj": "โฃ",
		"title": "Heavy Heart Exclamation"
	},
	{
		"emj": "๐",
		"title": "Love Letter"
	},
	{
		"emj": "๐ค",
		"title": "Zzz"
	},
	{
		"emj": "๐ข",
		"title": "Anger Symbol"
	},
	{
		"emj": "๐ฃ",
		"title": "Bomb"
	},
	{
		"emj": "๐ฅ",
		"title": "Collision"
	},
	{
		"emj": "๐ฆ",
		"title": "Sweat Droplets"
	},
	{
		"emj": "๐จ",
		"title": "Dashing Away"
	},
	{
		"emj": "๐ซ",
		"title": "Dizzy"
	},
	{
		"emj": "๐ฌ",
		"title": "Speech Balloon"
	},
	{
		"emj": "๐จ",
		"title": "Left Speech Bubble"
	},
	{
		"emj": "๐ฏ",
		"title": "Right Anger Bubble"
	},
	{
		"emj": "๐ญ",
		"title": "Thought Balloon"
	},
	{
		"emj": "๐ณ",
		"title": "Hole"
	},
	{
		"emj": "๐",
		"title": "Glasses"
	},
	{
		"emj": "๐ถ",
		"title": "Sunglasses"
	},
	{
		"emj": "๐",
		"title": "Necktie"
	},
	{
		"emj": "๐",
		"title": "T-shirt"
	},
	{
		"emj": "๐",
		"title": "Jeans"
	},
	{
		"emj": "๐งฃ",
		"title": "Scarf"
	},
	{
		"emj": "๐งค",
		"title": "Gloves"
	},
	{
		"emj": "๐งฅ",
		"title": "Coat"
	},
	{
		"emj": "๐งฆ",
		"title": "Socks"
	},
	{
		"emj": "๐",
		"title": "Dress"
	},
	{
		"emj": "๐",
		"title": "Kimono"
	},
	{
		"emj": "๐",
		"title": "Bikini"
	},
	{
		"emj": "๐",
		"title": "Womanโs Clothes"
	},
	{
		"emj": "๐",
		"title": "Purse"
	},
	{
		"emj": "๐",
		"title": "Handbag"
	},
	{
		"emj": "๐",
		"title": "Clutch Bag"
	},
	{
		"emj": "๐",
		"title": "Shopping Bags"
	},
	{
		"emj": "๐",
		"title": "Backpack"
	},
	{
		"emj": "๐",
		"title": "Manโs Shoe"
	},
	{
		"emj": "๐",
		"title": "Running Shoe"
	},
	{
		"emj": "๐?",
		"title": "High-heeled Shoe"
	},
	{
		"emj": "๐ก",
		"title": "Womanโs Sandal"
	},
	{
		"emj": "๐ข",
		"title": "Womanโs Boot"
	},
	{
		"emj": "๐",
		"title": "Crown"
	},
	{
		"emj": "๐",
		"title": "Womanโs Hat"
	},
	{
		"emj": "๐ฉ",
		"title": "Top Hat"
	},
	{
		"emj": "๐",
		"title": "Graduation Cap"
	},
	{
		"emj": "๐งข",
		"title": "Billed Cap"
	},
	{
		"emj": "โ",
		"title": "Rescue Workerโs Helmet"
	},
	{
		"emj": "๐ฟ",
		"title": "Prayer Beads"
	},
	{
		"emj": "๐",
		"title": "Lipstick"
	},
	{
		"emj": "๐",
		"title": "Ring"
	},
	{
		"emj": "๐",
		"title": "Gem Stone"
	}
	]
},
{	
	"name": "Nature",
	"emojies": [
	{
		"emj": "๐ต",
		"title": "Monkey Face"
	},
	{
		"emj": "๐",
		"title": "Monkey"
	},
	{
		"emj": "๐ฆ",
		"title": "Gorilla"
	},
	{
		"emj": "๐ถ",
		"title": "Dog Face"
	},
	{
		"emj": "๐",
		"title": "Dog"
	},
	{
		"emj": "๐ฉ",
		"title": "Poodle"
	},
	{
		"emj": "๐บ",
		"title": "Wolf Face"
	},
	{
		"emj": "๐ฆ",
		"title": "Fox Face"
	},
	{
		"emj": "๐ฑ",
		"title": "Cat Face"
	},
	{
		"emj": "๐",
		"title": "Cat"
	},
	{
		"emj": "๐ฆ",
		"title": "Lion Face"
	},
	{
		"emj": "๐ฏ",
		"title": "Tiger Face"
	},
	{
		"emj": "๐",
		"title": "Tiger"
	},
	{
		"emj": "๐",
		"title": "Leopard"
	},
	{
		"emj": "๐ด",
		"title": "Horse Face"
	},
	{
		"emj": "๐",
		"title": "Horse"
	},
	{
		"emj": "๐ฆ",
		"title": "Unicorn Face"
	},
	{
		"emj": "๐ฆ",
		"title": "Zebra"
	},
	{
		"emj": "๐ฆ",
		"title": "Deer"
	},
	{
		"emj": "๐ฎ",
		"title": "Cow Face"
	},
	{
		"emj": "๐",
		"title": "Ox"
	},
	{
		"emj": "๐",
		"title": "Water Buffalo"
	},
	{
		"emj": "๐",
		"title": "Cow"
	},
	{
		"emj": "๐ท",
		"title": "Pig Face"
	},
	{
		"emj": "๐",
		"title": "Pig"
	},
	{
		"emj": "๐",
		"title": "Boar"
	},
	{
		"emj": "๐ฝ",
		"title": "Pig Nose"
	},
	{
		"emj": "๐",
		"title": "Ram"
	},
	{
		"emj": "๐",
		"title": "Ewe"
	},
	{
		"emj": "๐",
		"title": "Goat"
	},
	{
		"emj": "๐ช",
		"title": "Camel"
	},
	{
		"emj": "๐ซ",
		"title": "Two-hump Camel"
	},
	{
		"emj": "๐ฆ",
		"title": "Giraffe"
	},
	{
		"emj": "๐",
		"title": "Elephant"
	},
	{
		"emj": "๐ฆ",
		"title": "Rhinoceros"
	},
	{
		"emj": "๐ญ",
		"title": "Mouse Face"
	},
	{
		"emj": "๐",
		"title": "Mouse"
	},
	{
		"emj": "๐",
		"title": "Rat"
	},
	{
		"emj": "๐น",
		"title": "Hamster Face"
	},
	{
		"emj": "๐ฐ",
		"title": "Rabbit Face"
	},
	{
		"emj": "๐",
		"title": "Rabbit"
	},
	{
		"emj": "๐ฟ",
		"title": "Chipmunk"
	},
	{
		"emj": "๐ฆ",
		"title": "Hedgehog"
	},
	{
		"emj": "๐ฆ",
		"title": "Bat"
	},
	{
		"emj": "๐ป",
		"title": "Bear Face"
	},
	{
		"emj": "๐จ",
		"title": "Koala"
	},
	{
		"emj": "๐ผ",
		"title": "Panda Face"
	},
	{
		"emj": "๐พ",
		"title": "Paw Prints"
	},
	{
		"emj": "๐ฆ",
		"title": "Turkey"
	},
	{
		"emj": "๐",
		"title": "Chicken"
	},
	{
		"emj": "๐",
		"title": "Rooster"
	},
	{
		"emj": "๐ฃ",
		"title": "Hatching Chick"
	},
	{
		"emj": "๐ค",
		"title": "Baby Chick"
	},
	{
		"emj": "๐ฅ",
		"title": "Front-facing Baby Chick"
	},
	{
		"emj": "๐ฆ",
		"title": "Bird"
	},
	{
		"emj": "๐ง",
		"title": "Penguin"
	},
	{
		"emj": "๐",
		"title": "Dove"
	},
	{
		"emj": "๐ฆ",
		"title": "Eagle"
	},
	{
		"emj": "๐ฆ",
		"title": "Duck"
	},
	{
		"emj": "๐ฆ",
		"title": "Owl"
	},
	{
		"emj": "๐ธ",
		"title": "Frog Face"
	},
	{
		"emj": "๐",
		"title": "Crocodile"
	},
	{
		"emj": "๐ข",
		"title": "Turtle"
	},
	{
		"emj": "๐ฆ",
		"title": "Lizard"
	},
	{
		"emj": "๐",
		"title": "Snake"
	},
	{
		"emj": "๐ฒ",
		"title": "Dragon Face"
	},
	{
		"emj": "๐",
		"title": "Dragon"
	},
	{
		"emj": "๐ฆ",
		"title": "Sauropod"
	},
	{
		"emj": "๐ฆ",
		"title": "T-Rex"
	},
	{
		"emj": "๐ณ",
		"title": "Spouting Whale"
	},
	{
		"emj": "๐",
		"title": "Whale"
	},
	{
		"emj": "๐ฌ",
		"title": "Dolphin"
	},
	{
		"emj": "๐",
		"title": "Fish"
	},
	{
		"emj": "๐?",
		"title": "Tropical Fish"
	},
	{
		"emj": "๐ก",
		"title": "Blowfish"
	},
	{
		"emj": "๐ฆ",
		"title": "Shark"
	},
	{
		"emj": "๐",
		"title": "Octopus"
	},
	{
		"emj": "๐",
		"title": "Spiral Shell"
	},
	{
		"emj": "๐ฆ",
		"title": "Crab"
	},
	{
		"emj": "๐ฆ",
		"title": "Shrimp"
	},
	{
		"emj": "๐ฆ",
		"title": "Squid"
	},
	{
		"emj": "๐",
		"title": "Snail"
	},
	{
		"emj": "๐ฆ",
		"title": "Butterfly"
	},
	{
		"emj": "๐",
		"title": "Bug"
	},
	{
		"emj": "๐",
		"title": "Ant"
	},
	{
		"emj": "๐",
		"title": "Honeybee"
	},
	{
		"emj": "๐",
		"title": "Lady Beetle"
	},
	{
		"emj": "๐ฆ",
		"title": "Cricket"
	},
	{
		"emj": "๐ท",
		"title": "Spider"
	},
	{
		"emj": "๐ธ",
		"title": "Spider Web"
	},
	{
		"emj": "๐ฆ",
		"title": "Scorpion"
	},
	{
		"emj": "๐",
		"title": "Bouquet"
	},
	{
		"emj": "๐ธ",
		"title": "Cherry Blossom"
	},
	{
		"emj": "๐ฎ",
		"title": "White Flower"
	},
	{
		"emj": "๐ต",
		"title": "Rosette"
	},
	{
		"emj": "๐น",
		"title": "Rose"
	},
	{
		"emj": "๐ฅ",
		"title": "Wilted Flower"
	},
	{
		"emj": "๐บ",
		"title": "Hibiscus"
	},
	{
		"emj": "๐ป",
		"title": "Sunflower"
	},
	{
		"emj": "๐ผ",
		"title": "Blossom"
	},
	{
		"emj": "๐ท",
		"title": "Tulip"
	},
	{
		"emj": "๐ฑ",
		"title": "Seedling"
	},
	{
		"emj": "๐ฒ",
		"title": "Evergreen Tree"
	},
	{
		"emj": "๐ณ",
		"title": "Deciduous Tree"
	},
	{
		"emj": "๐ด",
		"title": "Palm Tree"
	},
	{
		"emj": "๐ต",
		"title": "Cactus"
	},
	{
		"emj": "๐พ",
		"title": "Sheaf Of Rice"
	},
	{
		"emj": "๐ฟ",
		"title": "Herb"
	},
	{
		"emj": "โ",
		"title": "Shamrock"
	},
	{
		"emj": "๐",
		"title": "Four Leaf Clover"
	},
	{
		"emj": "๐",
		"title": "Maple Leaf"
	},
	{
		"emj": "๐",
		"title": "Fallen Leaf"
	},
	{
		"emj": "๐",
		"title": "Leaf Fluttering In Wind"
	}
	]
},
{
	"name": "Foof & Drink",
	"emojies": [
	{
		"emj": "๐",
		"title": "Grapes"
	},
	{
		"emj": "๐",
		"title": "Melon"
	},
	{
		"emj": "๐",
		"title": "Watermelon"
	},
	{
		"emj": "๐",
		"title": "Tangerine"
	},
	{
		"emj": "๐",
		"title": "Lemon"
	},
	{
		"emj": "๐",
		"title": "Banana"
	},
	{
		"emj": "๐",
		"title": "Pineapple"
	},
	{
		"emj": "๐",
		"title": "Red Apple"
	},
	{
		"emj": "๐",
		"title": "Green Apple"
	},
	{
		"emj": "๐",
		"title": "Pear"
	},
	{
		"emj": "๐",
		"title": "Peach"
	},
	{
		"emj": "๐",
		"title": "Cherries"
	},
	{
		"emj": "๐",
		"title": "Strawberry"
	},
	{
		"emj": "๐ฅ",
		"title": "Kiwi Fruit"
	},
	{
		"emj": "๐",
		"title": "Tomato"
	},
	{
		"emj": "๐ฅฅ",
		"title": "Coconut"
	},
	{
		"emj": "๐ฅ",
		"title": "Avocado"
	},
	{
		"emj": "๐",
		"title": "Eggplant"
	},
	{
		"emj": "๐ฅ",
		"title": "Potato"
	},
	{
		"emj": "๐ฅ",
		"title": "Carrot"
	},
	{
		"emj": "๐ฝ",
		"title": "Ear Of Corn"
	},
	{
		"emj": "๐ถ",
		"title": "Hot Pepper"
	},
	{
		"emj": "๐ฅ",
		"title": "Cucumber"
	},
	{
		"emj": "๐ฅฆ",
		"title": "Broccoli"
	},
	{
		"emj": "๐",
		"title": "Mushroom"
	},
	{
		"emj": "๐ฅ",
		"title": "Peanuts"
	},
	{
		"emj": "๐ฐ",
		"title": "Chestnut"
	},
	{
		"emj": "๐",
		"title": "Bread"
	},
	{
		"emj": "๐ฅ",
		"title": "Croissant"
	},
	{
		"emj": "๐ฅ",
		"title": "Baguette Bread"
	},
	{
		"emj": "๐ฅจ",
		"title": "Pretzel"
	},
	{
		"emj": "๐ฅ",
		"title": "Pancakes"
	},
	{
		"emj": "๐ง",
		"title": "Cheese Wedge"
	},
	{
		"emj": "๐",
		"title": "Meat On Bone"
	},
	{
		"emj": "๐",
		"title": "Poultry Leg"
	},
	{
		"emj": "๐ฅฉ",
		"title": "Cut Of Meat"
	},
	{
		"emj": "๐ฅ",
		"title": "Bacon"
	},
	{
		"emj": "๐",
		"title": "Hamburger"
	},
	{
		"emj": "๐",
		"title": "French Fries"
	},
	{
		"emj": "๐",
		"title": "Pizza"
	},
	{
		"emj": "๐ญ",
		"title": "Hot Dog"
	},
	{
		"emj": "๐ฅช",
		"title": "Sandwich"
	},
	{
		"emj": "๐ฎ",
		"title": "Taco"
	},
	{
		"emj": "๐ฏ",
		"title": "Burrito"
	},
	{
		"emj": "๐ฅ",
		"title": "Stuffed Flatbread"
	},
	{
		"emj": "๐ฅ",
		"title": "Egg"
	},
	{
		"emj": "๐ณ",
		"title": "Cooking"
	},
	{
		"emj": "๐ฅ",
		"title": "Shallow Pan Of Food"
	},
	{
		"emj": "๐ฒ",
		"title": "Pot Of Food"
	},
	{
		"emj": "๐ฅฃ",
		"title": "Bowl With Spoon"
	},
	{
		"emj": "๐ฅ",
		"title": "Green Salad"
	},
	{
		"emj": "๐ฟ",
		"title": "Popcorn"
	},
	{
		"emj": "๐ฅซ",
		"title": "Canned Food"
	},
	{
		"emj": "๐ฑ",
		"title": "Bento Box"
	},
	{
		"emj": "๐",
		"title": "Rice Cracker"
	},
	{
		"emj": "๐",
		"title": "Rice Ball"
	},
	{
		"emj": "๐",
		"title": "Cooked Rice"
	},
	{
		"emj": "๐",
		"title": "Curry Rice"
	},
	{
		"emj": "๐",
		"title": "Steaming Bowl"
	},
	{
		"emj": "๐",
		"title": "Spaghetti"
	},
	{
		"emj": "๐?",
		"title": "Roasted Sweet Potato"
	},
	{
		"emj": "๐ข",
		"title": "Oden"
	},
	{
		"emj": "๐ฃ",
		"title": "Sushi"
	},
	{
		"emj": "๐ค",
		"title": "Fried Shrimp"
	},
	{
		"emj": "๐ฅ",
		"title": "Fish Cake With Swirl"
	},
	{
		"emj": "๐ก",
		"title": "Dango"
	},
	{
		"emj": "๐ฅ",
		"title": "Dumpling"
	},
	{
		"emj": "๐ฅ?",
		"title": "Fortune Cookie"
	},
	{
		"emj": "๐ฅก",
		"title": "Takeout Box"
	},
	{
		"emj": "๐ฆ",
		"title": "Soft Ice Cream"
	},
	{
		"emj": "๐ง",
		"title": "Shaved Ice"
	},
	{
		"emj": "๐จ",
		"title": "Ice Cream"
	},
	{
		"emj": "๐ฉ",
		"title": "Doughnut"
	},
	{
		"emj": "๐ช",
		"title": "Cookie"
	},
	{
		"emj": "๐",
		"title": "Birthday Cake"
	},
	{
		"emj": "๐ฐ",
		"title": "Shortcake"
	},
	{
		"emj": "๐ฅง",
		"title": "Pie"
	},
	{
		"emj": "๐ซ",
		"title": "Chocolate Bar"
	},
	{
		"emj": "๐ฌ",
		"title": "Candy"
	},
	{
		"emj": "๐ญ",
		"title": "Lollipop"
	},
	{
		"emj": "๐ฎ",
		"title": "Custard"
	},
	{
		"emj": "๐ฏ",
		"title": "Honey Pot"
	},
	{
		"emj": "๐ผ",
		"title": "Baby Bottle"
	},
	{
		"emj": "๐ฅ",
		"title": "Glass Of Milk"
	},
	{
		"emj": "โ",
		"title": "Hot Beverage"
	},
	{
		"emj": "๐ต",
		"title": "Teacup Without Handle"
	},
	{
		"emj": "๐ถ",
		"title": "Sake"
	},
	{
		"emj": "๐พ",
		"title": "Bottle With Popping Cork"
	},
	{
		"emj": "๐ท",
		"title": "Wine Glass"
	},
	{
		"emj": "๐ธ",
		"title": "Cocktail Glass"
	},
	{
		"emj": "๐น",
		"title": "Tropical Drink"
	},
	{
		"emj": "๐บ",
		"title": "Beer Mug"
	},
	{
		"emj": "๐ป",
		"title": "Clinking Beer Mugs"
	},
	{
		"emj": "๐ฅ",
		"title": "Clinking Glasses"
	},
	{
		"emj": "๐ฅ",
		"title": "Tumbler Glass"
	},
	{
		"emj": "๐ฅค",
		"title": "Cup With Straw"
	},
	{
		"emj": "๐ฅข",
		"title": "Chopsticks"
	},
	{
		"emj": "๐ฝ",
		"title": "Fork And Knife With Plate"
	},
	{
		"emj": "๐ด",
		"title": "Fork And Knife"
	},
	{
		"emj": "๐ฅ",
		"title": "Spoon"
	},
	{
		"emj": "๐ช",
		"title": "Kitchen Knife"
	},
	{
		"emj": "๐บ",
		"title": "Amphora"
	}
	]
},
{
	"name": "Activities",
	"emojies": [
	{
		"emj": "๐",
		"title": "Jack-o-lantern"
	},
	{
		"emj": "๐",
		"title": "Christmas Tree"
	},
	{
		"emj": "๐",
		"title": "Fireworks"
	},
	{
		"emj": "๐",
		"title": "Sparkler"
	},
	{
		"emj": "โจ",
		"title": "Sparkles"
	},
	{
		"emj": "๐",
		"title": "Balloon"
	},
	{
		"emj": "๐",
		"title": "Party Popper"
	},
	{
		"emj": "๐",
		"title": "Confetti Ball"
	},
	{
		"emj": "๐",
		"title": "Tanabata Tree"
	},
	{
		"emj": "๐",
		"title": "Pine Decoration"
	},
	{
		"emj": "๐",
		"title": "Japanese Dolls"
	},
	{
		"emj": "๐",
		"title": "Carp Streamer"
	},
	{
		"emj": "๐",
		"title": "Wind Chime"
	},
	{
		"emj": "๐",
		"title": "Moon Viewing Ceremony"
	},
	{
		"emj": "๐",
		"title": "Ribbon"
	},
	{
		"emj": "๐",
		"title": "Wrapped Gift"
	},
	{
		"emj": "๐",
		"title": "Reminder Ribbon"
	},
	{
		"emj": "๐",
		"title": "Admission Tickets"
	},
	{
		"emj": "๐ซ",
		"title": "Ticket"
	},
	{
		"emj": "๐",
		"title": "Military Medal"
	},
	{
		"emj": "๐",
		"title": "Trophy"
	},
	{
		"emj": "๐",
		"title": "Sports Medal"
	},
	{
		"emj": "๐ฅ",
		"title": "1st Place Medal"
	},
	{
		"emj": "๐ฅ",
		"title": "2nd Place Medal"
	},
	{
		"emj": "๐ฅ",
		"title": "3rd Place Medal"
	},
	{
		"emj": "โฝ",
		"title": "Soccer Ball"
	},
	{
		"emj": "โพ",
		"title": "Baseball"
	},
	{
		"emj": "๐",
		"title": "Basketball"
	},
	{
		"emj": "๐",
		"title": "Volleyball"
	},
	{
		"emj": "๐",
		"title": "American Football"
	},
	{
		"emj": "๐",
		"title": "Rugby Football"
	},
	{
		"emj": "๐พ",
		"title": "Tennis"
	},
	{
		"emj": "๐ณ",
		"title": "Bowling"
	},
	{
		"emj": "๐",
		"title": "Cricket Game"
	},
	{
		"emj": "๐",
		"title": "Field Hockey"
	},
	{
		"emj": "๐",
		"title": "Ice Hockey"
	},
	{
		"emj": "๐",
		"title": "Ping Pong"
	},
	{
		"emj": "๐ธ",
		"title": "Badminton"
	},
	{
		"emj": "๐ฅ",
		"title": "Boxing Glove"
	},
	{
		"emj": "๐ฅ",
		"title": "Martial Arts Uniform"
	},
	{
		"emj": "๐ฅ",
		"title": "Goal Net"
	},
	{
		"emj": "โณ",
		"title": "Flag In Hole"
	},
	{
		"emj": "โธ",
		"title": "Ice Skate"
	},
	{
		"emj": "๐ฃ",
		"title": "Fishing Pole"
	},
	{
		"emj": "๐ฝ",
		"title": "Running Shirt"
	},
	{
		"emj": "๐ฟ",
		"title": "Skis"
	},
	{
		"emj": "๐ท",
		"title": "Sled"
	},
	{
		"emj": "๐ฅ",
		"title": "Curling Stone"
	},
	{
		"emj": "๐ฏ",
		"title": "Direct Hit"
	},
	{
		"emj": "๐ฑ",
		"title": "Pool 8 Ball"
	},
	{
		"emj": "๐ฎ",
		"title": "Crystal Ball"
	},
	{
		"emj": "๐ฎ",
		"title": "Video Game"
	},
	{
		"emj": "๐น",
		"title": "Joystick"
	},
	{
		"emj": "๐ฐ",
		"title": "Slot Machine"
	},
	{
		"emj": "๐ฒ",
		"title": "Game Die"
	},
	{
		"emj": "โ?",
		"title": "Spade Suit"
	},
	{
		"emj": "โฅ",
		"title": "Heart Suit"
	},
	{
		"emj": "โฆ",
		"title": "Diamond Suit"
	},
	{
		"emj": "โฃ",
		"title": "Club Suit"
	},
	{
		"emj": "๐",
		"title": "Joker"
	},
	{
		"emj": "๐",
		"title": "Mahjong Red Dragon"
	},
	{
		"emj": "๐ด",
		"title": "Flower Playing Cards"
	},
	{
		"emj": "๐ญ",
		"title": "Performing Arts"
	},
	{
		"emj": "๐ผ",
		"title": "Framed Picture"
	},
	{
		"emj": "๐จ",
		"title": "Artist Palette"
	}
	]
},
{
	"name": "Travel & Places",
	"emojies": [
	{
		"emj": "๐",
		"title": "Globe Showing Europe-Africa"
	},
	{
		"emj": "๐",
		"title": "Globe Showing Americas"
	},
	{
		"emj": "๐",
		"title": "Globe Showing Asia-Australia"
	},
	{
		"emj": "๐",
		"title": "Globe With Meridians"
	},
	{
		"emj": "๐บ",
		"title": "World Map"
	},
	{
		"emj": "๐พ",
		"title": "Map Of Japan"
	},
	{
		"emj": "๐",
		"title": "Snow-capped Mountain"
	},
	{
		"emj": "โฐ",
		"title": "Mountain"
	},
	{
		"emj": "๐",
		"title": "Volcano"
	},
	{
		"emj": "๐ป",
		"title": "Mount Fuji"
	},
	{
		"emj": "๐",
		"title": "Camping"
	},
	{
		"emj": "๐",
		"title": "Beach With Umbrella"
	},
	{
		"emj": "๐",
		"title": "Desert"
	},
	{
		"emj": "๐",
		"title": "Desert Island"
	},
	{
		"emj": "๐",
		"title": "National Park"
	},
	{
		"emj": "๐",
		"title": "Stadium"
	},
	{
		"emj": "๐",
		"title": "Classical Building"
	},
	{
		"emj": "๐",
		"title": "Building Construction"
	},
	{
		"emj": "๐",
		"title": "Houses"
	},
	{
		"emj": "๐",
		"title": "Derelict House"
	},
	{
		"emj": "๐?",
		"title": "House"
	},
	{
		"emj": "๐ก",
		"title": "House With Garden"
	},
	{
		"emj": "๐ข",
		"title": "Office Building"
	},
	{
		"emj": "๐ฃ",
		"title": "Japanese Post Office"
	},
	{
		"emj": "๐ค",
		"title": "Post Office"
	},
	{
		"emj": "๐ฅ",
		"title": "Hospital"
	},
	{
		"emj": "๐ฆ",
		"title": "Bank"
	},
	{
		"emj": "๐จ",
		"title": "Hotel"
	},
	{
		"emj": "๐ฉ",
		"title": "Love Hotel"
	},
	{
		"emj": "๐ช",
		"title": "Convenience Store"
	},
	{
		"emj": "๐ซ",
		"title": "School"
	},
	{
		"emj": "๐ฌ",
		"title": "Department Store"
	},
	{
		"emj": "๐ญ",
		"title": "Factory"
	},
	{
		"emj": "๐ฏ",
		"title": "Japanese Castle"
	},
	{
		"emj": "๐ฐ",
		"title": "Castle"
	},
	{
		"emj": "๐",
		"title": "Wedding"
	},
	{
		"emj": "๐ผ",
		"title": "Tokyo Tower"
	},
	{
		"emj": "๐ฝ",
		"title": "Statue Of Liberty"
	},
	{
		"emj": "โช",
		"title": "Church"
	},
	{
		"emj": "๐",
		"title": "Mosque"
	},
	{
		"emj": "๐",
		"title": "Synagogue"
	},
	{
		"emj": "โฉ",
		"title": "Shinto Shrine"
	},
	{
		"emj": "๐",
		"title": "Kaaba"
	},
	{
		"emj": "โฒ",
		"title": "Fountain"
	},
	{
		"emj": "โบ",
		"title": "Tent"
	},
	{
		"emj": "๐",
		"title": "Foggy"
	},
	{
		"emj": "๐",
		"title": "Night With Stars"
	},
	{
		"emj": "๐",
		"title": "Cityscape"
	},
	{
		"emj": "๐",
		"title": "Sunrise Over Mountains"
	},
	{
		"emj": "๐",
		"title": "Sunrise"
	},
	{
		"emj": "๐",
		"title": "Cityscape At Dusk"
	},
	{
		"emj": "๐",
		"title": "Sunset"
	},
	{
		"emj": "๐",
		"title": "Bridge At Night"
	},
	{
		"emj": "โจ",
		"title": "Hot Springs"
	},
	{
		"emj": "๐",
		"title": "Milky Way"
	},
	{
		"emj": "๐?",
		"title": "Carousel Horse"
	},
	{
		"emj": "๐ก",
		"title": "Ferris Wheel"
	},
	{
		"emj": "๐ข",
		"title": "Roller Coaster"
	},
	{
		"emj": "๐",
		"title": "Barber Pole"
	},
	{
		"emj": "๐ช",
		"title": "Circus Tent"
	},
	{
		"emj": "๐",
		"title": "Locomotive"
	},
	{
		"emj": "๐",
		"title": "Railway Car"
	},
	{
		"emj": "๐",
		"title": "High-speed Train"
	},
	{
		"emj": "๐",
		"title": "Bullet Train"
	},
	{
		"emj": "๐",
		"title": "Train"
	},
	{
		"emj": "๐",
		"title": "Metro"
	},
	{
		"emj": "๐",
		"title": "Light Rail"
	},
	{
		"emj": "๐",
		"title": "Station"
	},
	{
		"emj": "๐",
		"title": "Tram"
	},
	{
		"emj": "๐",
		"title": "Monorail"
	},
	{
		"emj": "๐",
		"title": "Mountain Railway"
	},
	{
		"emj": "๐",
		"title": "Tram Car"
	},
	{
		"emj": "๐",
		"title": "Bus"
	},
	{
		"emj": "๐",
		"title": "Oncoming Bus"
	},
	{
		"emj": "๐",
		"title": "Trolleybus"
	},
	{
		"emj": "๐",
		"title": "Minibus"
	},
	{
		"emj": "๐",
		"title": "Ambulance"
	},
	{
		"emj": "๐",
		"title": "Fire Engine"
	},
	{
		"emj": "๐",
		"title": "Police Car"
	},
	{
		"emj": "๐",
		"title": "Oncoming Police Car"
	},
	{
		"emj": "๐",
		"title": "Taxi"
	},
	{
		"emj": "๐",
		"title": "Oncoming Taxi"
	},
	{
		"emj": "๐",
		"title": "Automobile"
	},
	{
		"emj": "๐",
		"title": "Oncoming Automobile"
	},
	{
		"emj": "๐",
		"title": "Sport Utility Vehicle"
	},
	{
		"emj": "๐",
		"title": "Delivery Truck"
	},
	{
		"emj": "๐",
		"title": "Articulated Lorry"
	},
	{
		"emj": "๐",
		"title": "Tractor"
	},
	{
		"emj": "๐ฒ",
		"title": "Bicycle"
	},
	{
		"emj": "๐ด",
		"title": "Kick Scooter"
	},
	{
		"emj": "๐ต",
		"title": "Motor Scooter"
	},
	{
		"emj": "๐",
		"title": "Bus Stop"
	},
	{
		"emj": "๐ฃ",
		"title": "Motorway"
	},
	{
		"emj": "๐ค",
		"title": "Railway Track"
	},
	{
		"emj": "๐ข",
		"title": "Oil Drum"
	},
	{
		"emj": "โฝ",
		"title": "Fuel Pump"
	},
	{
		"emj": "๐จ",
		"title": "Police Car Light"
	},
	{
		"emj": "๐ฅ",
		"title": "Horizontal Traffic Light"
	},
	{
		"emj": "๐ฆ",
		"title": "Vertical Traffic Light"
	},
	{
		"emj": "๐",
		"title": "Stop Sign"
	},
	{
		"emj": "๐ง",
		"title": "Construction"
	},
	{
		"emj": "โ",
		"title": "Anchor"
	},
	{
		"emj": "โต",
		"title": "Sailboat"
	},
	{
		"emj": "๐ถ",
		"title": "Canoe"
	},
	{
		"emj": "๐ค",
		"title": "Speedboat"
	},
	{
		"emj": "๐ณ",
		"title": "Passenger Ship"
	},
	{
		"emj": "โด",
		"title": "Ferry"
	},
	{
		"emj": "๐ฅ",
		"title": "Motor Boat"
	},
	{
		"emj": "๐ข",
		"title": "Ship"
	},
	{
		"emj": "โ",
		"title": "Airplane"
	},
	{
		"emj": "๐ฉ",
		"title": "Small Airplane"
	},
	{
		"emj": "๐ซ",
		"title": "Airplane Departure"
	},
	{
		"emj": "๐ฌ",
		"title": "Airplane Arrival"
	},
	{
		"emj": "๐บ",
		"title": "Seat"
	},
	{
		"emj": "๐",
		"title": "Helicopter"
	},
	{
		"emj": "๐",
		"title": "Suspension Railway"
	},
	{
		"emj": "๐?",
		"title": "Mountain Cableway"
	},
	{
		"emj": "๐ก",
		"title": "Aerial Tramway"
	},
	{
		"emj": "๐ฐ",
		"title": "Satellite"
	},
	{
		"emj": "๐",
		"title": "Rocket"
	},
	{
		"emj": "๐ธ",
		"title": "Flying Saucer"
	},
	{
		"emj": "๐",
		"title": "Bellhop Bell"
	},
	{
		"emj": "โ",
		"title": "Hourglass Done"
	},
	{
		"emj": "โณ",
		"title": "Hourglass Not Done"
	},
	{
		"emj": "โ",
		"title": "Watch"
	},
	{
		"emj": "โฐ",
		"title": "Alarm Clock"
	},
	{
		"emj": "โฑ",
		"title": "Stopwatch"
	},
	{
		"emj": "โฒ",
		"title": "Timer Clock"
	},
	{
		"emj": "๐ฐ",
		"title": "Mantelpiece Clock"
	},
	{
		"emj": "๐",
		"title": "Twelve Oโclock"
	},
	{
		"emj": "๐ง",
		"title": "Twelve-thirty"
	},
	{
		"emj": "๐",
		"title": "One Oโclock"
	},
	{
		"emj": "๐",
		"title": "One-thirty"
	},
	{
		"emj": "๐",
		"title": "Two Oโclock"
	},
	{
		"emj": "๐",
		"title": "Two-thirty"
	},
	{
		"emj": "๐",
		"title": "Three Oโclock"
	},
	{
		"emj": "๐",
		"title": "Three-thirty"
	},
	{
		"emj": "๐",
		"title": "Four Oโclock"
	},
	{
		"emj": "๐",
		"title": "Four-thirty"
	},
	{
		"emj": "๐",
		"title": "Five Oโclock"
	},
	{
		"emj": "๐?",
		"title": "Five-thirty"
	},
	{
		"emj": "๐",
		"title": "Six Oโclock"
	},
	{
		"emj": "๐ก",
		"title": "Six-thirty"
	},
	{
		"emj": "๐",
		"title": "Seven Oโclock"
	},
	{
		"emj": "๐ข",
		"title": "Seven-thirty"
	},
	{
		"emj": "๐",
		"title": "Eight Oโclock"
	},
	{
		"emj": "๐ฃ",
		"title": "Eight-thirty"
	},
	{
		"emj": "๐",
		"title": "Nine Oโclock"
	},
	{
		"emj": "๐ค",
		"title": "Nine-thirty"
	},
	{
		"emj": "๐",
		"title": "Ten Oโclock"
	},
	{
		"emj": "๐ฅ",
		"title": "Ten-thirty"
	},
	{
		"emj": "๐",
		"title": "Eleven Oโclock"
	},
	{
		"emj": "๐ฆ",
		"title": "Eleven-thirty"
	},
	{
		"emj": "๐",
		"title": "New Moon"
	},
	{
		"emj": "๐",
		"title": "Waxing Crescent Moon"
	},
	{
		"emj": "๐",
		"title": "First Quarter Moon"
	},
	{
		"emj": "๐",
		"title": "Waxing Gibbous Moon"
	},
	{
		"emj": "๐",
		"title": "Full Moon"
	},
	{
		"emj": "๐",
		"title": "Waning Gibbous Moon"
	},
	{
		"emj": "๐",
		"title": "Last Quarter Moon"
	},
	{
		"emj": "๐",
		"title": "Waning Crescent Moon"
	},
	{
		"emj": "๐",
		"title": "Crescent Moon"
	},
	{
		"emj": "๐",
		"title": "New Moon Face"
	},
	{
		"emj": "๐",
		"title": "First Quarter Moon Face"
	},
	{
		"emj": "๐",
		"title": "Last Quarter Moon Face"
	},
	{
		"emj": "๐ก",
		"title": "Thermometer"
	},
	{
		"emj": "โ",
		"title": "Sun"
	},
	{
		"emj": "๐",
		"title": "Full Moon Face"
	},
	{
		"emj": "๐",
		"title": "Sun With Face"
	},
	{
		"emj": "โญ",
		"title": "Star"
	},
	{
		"emj": "๐",
		"title": "Glowing Star"
	},
	{
		"emj": "๐?",
		"title": "Shooting Star"
	},
	{
		"emj": "โ",
		"title": "Cloud"
	},
	{
		"emj": "โ",
		"title": "Sun Behind Cloud"
	},
	{
		"emj": "โ",
		"title": "Cloud With Lightning And Rain"
	},
	{
		"emj": "๐ค",
		"title": "Sun Behind Small Cloud"
	},
	{
		"emj": "๐ฅ",
		"title": "Sun Behind Large Cloud"
	},
	{
		"emj": "๐ฆ",
		"title": "Sun Behind Rain Cloud"
	},
	{
		"emj": "๐ง",
		"title": "Cloud With Rain"
	},
	{
		"emj": "๐จ",
		"title": "Cloud With Snow"
	},
	{
		"emj": "๐ฉ",
		"title": "Cloud With Lightning"
	},
	{
		"emj": "๐ช",
		"title": "Tornado"
	},
	{
		"emj": "๐ซ",
		"title": "Fog"
	},
	{
		"emj": "๐ฌ",
		"title": "Wind Face"
	},
	{
		"emj": "๐",
		"title": "Cyclone"
	},
	{
		"emj": "๐",
		"title": "Rainbow"
	},
	{
		"emj": "๐",
		"title": "Closed Umbrella"
	},
	{
		"emj": "โ",
		"title": "Umbrella"
	},
	{
		"emj": "โ",
		"title": "Umbrella With Rain Drops"
	},
	{
		"emj": "โฑ",
		"title": "Umbrella On Ground"
	},
	{
		"emj": "โก",
		"title": "High Voltage"
	},
	{
		"emj": "โ",
		"title": "Snowflake"
	},
	{
		"emj": "โ",
		"title": "Snowman"
	},
	{
		"emj": "โ",
		"title": "Snowman Without Snow"
	},
	{
		"emj": "โ",
		"title": "Comet"
	},
	{
		"emj": "๐ฅ",
		"title": "Fire"
	},
	{
		"emj": "๐ง",
		"title": "Droplet"
	},
	{
		"emj": "๐",
		"title": "Water Wave"
	}
	]
},
{
	"name": "Objects",
	"emojies": [
	{
		"emj": "๐",
		"title": "Muted Speaker"
	},
	{
		"emj": "๐",
		"title": "Speaker Low Volume"
	},
	{
		"emj": "๐",
		"title": "Speaker Medium Volume"
	},
	{
		"emj": "๐",
		"title": "Speaker High Volume"
	},
	{
		"emj": "๐ข",
		"title": "Loudspeaker"
	},
	{
		"emj": "๐ฃ",
		"title": "Megaphone"
	},
	{
		"emj": "๐ฏ",
		"title": "Postal Horn"
	},
	{
		"emj": "๐",
		"title": "Bell"
	},
	{
		"emj": "๐",
		"title": "Bell With Slash"
	},
	{
		"emj": "๐ผ",
		"title": "Musical Score"
	},
	{
		"emj": "๐ต",
		"title": "Musical Note"
	},
	{
		"emj": "๐ถ",
		"title": "Musical Notes"
	},
	{
		"emj": "๐",
		"title": "Studio Microphone"
	},
	{
		"emj": "๐",
		"title": "Level Slider"
	},
	{
		"emj": "๐",
		"title": "Control Knobs"
	},
	{
		"emj": "๐ค",
		"title": "Microphone"
	},
	{
		"emj": "๐ง",
		"title": "Headphone"
	},
	{
		"emj": "๐ป",
		"title": "Radio"
	},
	{
		"emj": "๐ท",
		"title": "Saxophone"
	},
	{
		"emj": "๐ธ",
		"title": "Guitar"
	},
	{
		"emj": "๐น",
		"title": "Musical Keyboard"
	},
	{
		"emj": "๐บ",
		"title": "Trumpet"
	},
	{
		"emj": "๐ป",
		"title": "Violin"
	},
	{
		"emj": "๐ฅ",
		"title": "Drum"
	},
	{
		"emj": "๐ฑ",
		"title": "Mobile Phone"
	},
	{
		"emj": "๐ฒ",
		"title": "Mobile Phone With Arrow"
	},
	{
		"emj": "โ",
		"title": "Telephone"
	},
	{
		"emj": "๐",
		"title": "Telephone Receiver"
	},
	{
		"emj": "๐",
		"title": "Pager"
	},
	{
		"emj": "๐?",
		"title": "Fax Machine"
	},
	{
		"emj": "๐",
		"title": "Battery"
	},
	{
		"emj": "๐",
		"title": "Electric Plug"
	},
	{
		"emj": "๐ป",
		"title": "Laptop Computer"
	},
	{
		"emj": "๐ฅ",
		"title": "Desktop Computer"
	},
	{
		"emj": "๐จ",
		"title": "Printer"
	},
	{
		"emj": "โจ",
		"title": "Keyboard"
	},
	{
		"emj": "๐ฑ",
		"title": "Computer Mouse"
	},
	{
		"emj": "๐ฒ",
		"title": "Trackball"
	},
	{
		"emj": "๐ฝ",
		"title": "Computer Disk"
	},
	{
		"emj": "๐พ",
		"title": "Floppy Disk"
	},
	{
		"emj": "๐ฟ",
		"title": "Optical Disk"
	},
	{
		"emj": "๐",
		"title": "Dvd"
	},
	{
		"emj": "๐ฅ",
		"title": "Movie Camera"
	},
	{
		"emj": "๐",
		"title": "Film Frames"
	},
	{
		"emj": "๐ฝ",
		"title": "Film Projector"
	},
	{
		"emj": "๐ฌ",
		"title": "Clapper Board"
	},
	{
		"emj": "๐บ",
		"title": "Television"
	},
	{
		"emj": "๐ท",
		"title": "Camera"
	},
	{
		"emj": "๐ธ",
		"title": "Camera With Flash"
	},
	{
		"emj": "๐น",
		"title": "Video Camera"
	},
	{
		"emj": "๐ผ",
		"title": "Videocassette"
	},
	{
		"emj": "๐",
		"title": "Magnifying Glass Tilted Left"
	},
	{
		"emj": "๐",
		"title": "Magnifying Glass Tilted Right"
	},
	{
		"emj": "๐ฏ",
		"title": "Candle"
	},
	{
		"emj": "๐ก",
		"title": "Light Bulb"
	},
	{
		"emj": "๐ฆ",
		"title": "Flashlight"
	},
	{
		"emj": "๐ฎ",
		"title": "Red Paper Lantern"
	},
	{
		"emj": "๐",
		"title": "Notebook With Decorative Cover"
	},
	{
		"emj": "๐",
		"title": "Closed Book"
	},
	{
		"emj": "๐",
		"title": "Open Book"
	},
	{
		"emj": "๐",
		"title": "Green Book"
	},
	{
		"emj": "๐",
		"title": "Blue Book"
	},
	{
		"emj": "๐",
		"title": "Orange Book"
	},
	{
		"emj": "๐",
		"title": "Books"
	},
	{
		"emj": "๐",
		"title": "Notebook"
	},
	{
		"emj": "๐",
		"title": "Ledger"
	},
	{
		"emj": "๐",
		"title": "Page With Curl"
	},
	{
		"emj": "๐",
		"title": "Scroll"
	},
	{
		"emj": "๐",
		"title": "Page Facing Up"
	},
	{
		"emj": "๐ฐ",
		"title": "Newspaper"
	},
	{
		"emj": "๐",
		"title": "Rolled-up Newspaper"
	},
	{
		"emj": "๐",
		"title": "Bookmark Tabs"
	},
	{
		"emj": "๐",
		"title": "Bookmark"
	},
	{
		"emj": "๐ท",
		"title": "Label"
	},
	{
		"emj": "๐ฐ",
		"title": "Money Bag"
	},
	{
		"emj": "๐ด",
		"title": "Yen Banknote"
	},
	{
		"emj": "๐ต",
		"title": "Dollar Banknote"
	},
	{
		"emj": "๐ถ",
		"title": "Euro Banknote"
	},
	{
		"emj": "๐ท",
		"title": "Pound Banknote"
	},
	{
		"emj": "๐ธ",
		"title": "Money With Wings"
	},
	{
		"emj": "๐ณ",
		"title": "Credit Card"
	},
	{
		"emj": "๐น",
		"title": "Chart Increasing With Yen"
	},
	{
		"emj": "๐ฑ",
		"title": "Currency Exchange"
	},
	{
		"emj": "๐ฒ",
		"title": "Heavy Dollar Sign"
	},
	{
		"emj": "โ",
		"title": "Envelope"
	},
	{
		"emj": "๐ง",
		"title": "E-mail"
	},
	{
		"emj": "๐จ",
		"title": "Incoming Envelope"
	},
	{
		"emj": "๐ฉ",
		"title": "Envelope With Arrow"
	},
	{
		"emj": "๐ค",
		"title": "Outbox Tray"
	},
	{
		"emj": "๐ฅ",
		"title": "Inbox Tray"
	},
	{
		"emj": "๐ฆ",
		"title": "Package"
	},
	{
		"emj": "๐ซ",
		"title": "Closed Mailbox With Raised Flag"
	},
	{
		"emj": "๐ช",
		"title": "Closed Mailbox With Lowered Flag"
	},
	{
		"emj": "๐ฌ",
		"title": "Open Mailbox With Raised Flag"
	},
	{
		"emj": "๐ญ",
		"title": "Open Mailbox With Lowered Flag"
	},
	{
		"emj": "๐ฎ",
		"title": "Postbox"
	},
	{
		"emj": "๐ณ",
		"title": "Ballot Box With Ballot"
	},
	{
		"emj": "โ",
		"title": "Pencil"
	},
	{
		"emj": "โ",
		"title": "Black Nib"
	},
	{
		"emj": "๐",
		"title": "Fountain Pen"
	},
	{
		"emj": "๐",
		"title": "Pen"
	},
	{
		"emj": "๐",
		"title": "Paintbrush"
	},
	{
		"emj": "๐",
		"title": "Crayon"
	},
	{
		"emj": "๐",
		"title": "Memo"
	},
	{
		"emj": "๐ผ",
		"title": "Briefcase"
	},
	{
		"emj": "๐",
		"title": "File Folder"
	},
	{
		"emj": "๐",
		"title": "Open File Folder"
	},
	{
		"emj": "๐",
		"title": "Card Index Dividers"
	},
	{
		"emj": "๐",
		"title": "Calendar"
	},
	{
		"emj": "๐",
		"title": "Tear-off Calendar"
	},
	{
		"emj": "๐",
		"title": "Spiral Notepad"
	},
	{
		"emj": "๐",
		"title": "Spiral Calendar"
	},
	{
		"emj": "๐",
		"title": "Card Index"
	},
	{
		"emj": "๐",
		"title": "Chart Increasing"
	},
	{
		"emj": "๐",
		"title": "Chart Decreasing"
	},
	{
		"emj": "๐",
		"title": "Bar Chart"
	},
	{
		"emj": "๐",
		"title": "Clipboard"
	},
	{
		"emj": "๐",
		"title": "Pushpin"
	},
	{
		"emj": "๐",
		"title": "Round Pushpin"
	},
	{
		"emj": "๐",
		"title": "Paperclip"
	},
	{
		"emj": "๐",
		"title": "Linked Paperclips"
	},
	{
		"emj": "๐",
		"title": "Straight Ruler"
	},
	{
		"emj": "๐",
		"title": "Triangular Ruler"
	},
	{
		"emj": "โ",
		"title": "Scissors"
	},
	{
		"emj": "๐",
		"title": "Card File Box"
	},
	{
		"emj": "๐",
		"title": "File Cabinet"
	},
	{
		"emj": "๐",
		"title": "Wastebasket"
	},
	{
		"emj": "๐",
		"title": "Locked"
	},
	{
		"emj": "๐",
		"title": "Unlocked"
	},
	{
		"emj": "๐",
		"title": "Locked With Pen"
	},
	{
		"emj": "๐",
		"title": "Locked With Key"
	},
	{
		"emj": "๐",
		"title": "Key"
	},
	{
		"emj": "๐",
		"title": "Old Key"
	},
	{
		"emj": "๐จ",
		"title": "Hammer"
	},
	{
		"emj": "โ",
		"title": "Pick"
	},
	{
		"emj": "โ",
		"title": "Hammer And Pick"
	},
	{
		"emj": "๐?",
		"title": "Hammer And Wrench"
	},
	{
		"emj": "๐ก",
		"title": "Dagger"
	},
	{
		"emj": "โ",
		"title": "Crossed Swords"
	},
	{
		"emj": "๐ซ",
		"title": "Pistol"
	},
	{
		"emj": "๐น",
		"title": "Bow And Arrow"
	},
	{
		"emj": "๐ก",
		"title": "Shield"
	},
	{
		"emj": "๐ง",
		"title": "Wrench"
	},
	{
		"emj": "๐ฉ",
		"title": "Nut And Bolt"
	},
	{
		"emj": "โ",
		"title": "Gear"
	},
	{
		"emj": "๐",
		"title": "Clamp"
	},
	{
		"emj": "โ",
		"title": "Balance Scale"
	},
	{
		"emj": "๐",
		"title": "Link"
	},
	{
		"emj": "โ",
		"title": "Chains"
	},
	{
		"emj": "โ",
		"title": "Alembic"
	},
	{
		"emj": "๐ฌ",
		"title": "Microscope"
	},
	{
		"emj": "๐ญ",
		"title": "Telescope"
	},
	{
		"emj": "๐ก",
		"title": "Satellite Antenna"
	},
	{
		"emj": "๐",
		"title": "Syringe"
	},
	{
		"emj": "๐",
		"title": "Pill"
	},
	{
		"emj": "๐ช",
		"title": "Door"
	},
	{
		"emj": "๐",
		"title": "Bed"
	},
	{
		"emj": "๐",
		"title": "Couch And Lamp"
	},
	{
		"emj": "๐ฝ",
		"title": "Toilet"
	},
	{
		"emj": "๐ฟ",
		"title": "Shower"
	},
	{
		"emj": "๐",
		"title": "Bathtub"
	},
	{
		"emj": "๐",
		"title": "Shopping Cart"
	},
	{
		"emj": "๐ฌ",
		"title": "Cigarette"
	},
	{
		"emj": "โฐ",
		"title": "Coffin"
	},
	{
		"emj": "โฑ",
		"title": "Funeral Urn"
	},
	{
		"emj": "๐ฟ",
		"title": "Moai"
	}
	]
},{
	"name": "Symbols",
	"emojies":[
	{
		"emj": "๐ง",
		"title": "ATM Sign"
	},
	{
		"emj": "๐ฎ",
		"title": "Litter In Bin Sign"
	},
	{
		"emj": "๐ฐ",
		"title": "Potable Water"
	},
	{
		"emj": "โฟ",
		"title": "Wheelchair Symbol"
	},
	{
		"emj": "๐น",
		"title": "Menโs Room"
	},
	{
		"emj": "๐บ",
		"title": "Womenโs Room"
	},
	{
		"emj": "๐ป",
		"title": "Restroom"
	},
	{
		"emj": "๐ผ",
		"title": "Baby Symbol"
	},
	{
		"emj": "๐พ",
		"title": "Water Closet"
	},
	{
		"emj": "๐",
		"title": "Passport Control"
	},
	{
		"emj": "๐",
		"title": "Customs"
	},
	{
		"emj": "๐",
		"title": "Baggage Claim"
	},
	{
		"emj": "๐",
		"title": "Left Luggage"
	},
	{
		"emj": "โ?",
		"title": "Warning"
	},
	{
		"emj": "๐ธ",
		"title": "Children Crossing"
	},
	{
		"emj": "โ",
		"title": "No Entry"
	},
	{
		"emj": "๐ซ",
		"title": "Prohibited"
	},
	{
		"emj": "๐ณ",
		"title": "No Bicycles"
	},
	{
		"emj": "๐ญ",
		"title": "No Smoking"
	},
	{
		"emj": "๐ฏ",
		"title": "No Littering"
	},
	{
		"emj": "๐ฑ",
		"title": "Non-potable Water"
	},
	{
		"emj": "๐ท",
		"title": "No Pedestrians"
	},
	{
		"emj": "๐ต",
		"title": "No Mobile Phones"
	},
	{
		"emj": "๐",
		"title": "No One Under Eighteen"
	},
	{
		"emj": "โข",
		"title": "Radioactive"
	},
	{
		"emj": "โฃ",
		"title": "Biohazard"
	},
	{
		"emj": "โฌ",
		"title": "Up Arrow"
	},
	{
		"emj": "โ",
		"title": "Up-right Arrow"
	},
	{
		"emj": "โก",
		"title": "Right Arrow"
	},
	{
		"emj": "โ",
		"title": "Down-right Arrow"
	},
	{
		"emj": "โฌ",
		"title": "Down Arrow"
	},
	{
		"emj": "โ",
		"title": "Down-left Arrow"
	},
	{
		"emj": "โฌ",
		"title": "Left Arrow"
	},
	{
		"emj": "โ",
		"title": "Up-left Arrow"
	},
	{
		"emj": "โ",
		"title": "Up-down Arrow"
	},
	{
		"emj": "โ",
		"title": "Left-right Arrow"
	},
	{
		"emj": "โฉ",
		"title": "Right Arrow Curving Left"
	},
	{
		"emj": "โช",
		"title": "Left Arrow Curving Right"
	},
	{
		"emj": "โคด",
		"title": "Right Arrow Curving Up"
	},
	{
		"emj": "โคต",
		"title": "Right Arrow Curving Down"
	},
	{
		"emj": "๐",
		"title": "Clockwise Vertical Arrows"
	},
	{
		"emj": "๐",
		"title": "Counterclockwise Arrows Button"
	},
	{
		"emj": "๐",
		"title": "BACK Arrow"
	},
	{
		"emj": "๐",
		"title": "END Arrow"
	},
	{
		"emj": "๐",
		"title": "ON! Arrow"
	},
	{
		"emj": "๐",
		"title": "SOON Arrow"
	},
	{
		"emj": "๐",
		"title": "TOP Arrow"
	},
	{
		"emj": "๐",
		"title": "Place Of Worship"
	},
	{
		"emj": "โ",
		"title": "Atom Symbol"
	},
	{
		"emj": "๐",
		"title": "Om"
	},
	{
		"emj": "โก",
		"title": "Star Of David"
	},
	{
		"emj": "โธ",
		"title": "Wheel Of Dharma"
	},
	{
		"emj": "โฏ",
		"title": "Yin Yang"
	},
	{
		"emj": "โ",
		"title": "Latin Cross"
	},
	{
		"emj": "โฆ",
		"title": "Orthodox Cross"
	},
	{
		"emj": "โช",
		"title": "Star And Crescent"
	},
	{
		"emj": "โฎ",
		"title": "Peace Symbol"
	},
	{
		"emj": "๐",
		"title": "Menorah"
	},
	{
		"emj": "๐ฏ",
		"title": "Dotted Six-pointed Star"
	},
	{
		"emj": "โ",
		"title": "Aries"
	},
	{
		"emj": "โ",
		"title": "Taurus"
	},
	{
		"emj": "โ",
		"title": "Gemini"
	},
	{
		"emj": "โ",
		"title": "Cancer"
	},
	{
		"emj": "โ",
		"title": "Leo"
	},
	{
		"emj": "โ",
		"title": "Virgo"
	},
	{
		"emj": "โ",
		"title": "Libra"
	},
	{
		"emj": "โ",
		"title": "Scorpio"
	},
	{
		"emj": "โ",
		"title": "Sagittarius"
	},
	{
		"emj": "โ",
		"title": "Capricorn"
	},
	{
		"emj": "โ",
		"title": "Aquarius"
	},
	{
		"emj": "โ",
		"title": "Pisces"
	},
	{
		"emj": "โ",
		"title": "Ophiuchus"
	},
	{
		"emj": "๐",
		"title": "Shuffle Tracks Button"
	},
	{
		"emj": "๐",
		"title": "Repeat Button"
	},
	{
		"emj": "๐",
		"title": "Repeat Single Button"
	},
	{
		"emj": "โถ",
		"title": "Play Button"
	},
	{
		"emj": "โฉ",
		"title": "Fast-forward Button"
	},
	{
		"emj": "โญ",
		"title": "Next Track Button"
	},
	{
		"emj": "โฏ",
		"title": "Play Or Pause Button"
	},
	{
		"emj": "โ",
		"title": "Reverse Button"
	},
	{
		"emj": "โช",
		"title": "Fast Reverse Button"
	},
	{
		"emj": "โฎ",
		"title": "Last Track Button"
	},
	{
		"emj": "๐ผ",
		"title": "Upwards Button"
	},
	{
		"emj": "โซ",
		"title": "Fast Up Button"
	},
	{
		"emj": "๐ฝ",
		"title": "Downwards Button"
	},
	{
		"emj": "โฌ",
		"title": "Fast Down Button"
	},
	{
		"emj": "โธ",
		"title": "Pause Button"
	},
	{
		"emj": "โน",
		"title": "Stop Button"
	},
	{
		"emj": "โบ",
		"title": "Record Button"
	},
	{
		"emj": "โ",
		"title": "Eject Button"
	},
	{
		"emj": "๐ฆ",
		"title": "Cinema"
	},
	{
		"emj": "๐",
		"title": "Dim Button"
	},
	{
		"emj": "๐",
		"title": "Bright Button"
	},
	{
		"emj": "๐ถ",
		"title": "Antenna Bars"
	},
	{
		"emj": "๐ณ",
		"title": "Vibration Mode"
	},
	{
		"emj": "๐ด",
		"title": "Mobile Phone Off"
	},
	{
		"emj": "โ",
		"title": "Female Sign"
	},
	{
		"emj": "โ",
		"title": "Male Sign"
	},
	{
		"emj": "โ",
		"title": "Medical Symbol"
	},
	{
		"emj": "โป",
		"title": "Recycling Symbol"
	},
	{
		"emj": "โ",
		"title": "Fleur-de-lis"
	},
	{
		"emj": "๐ฑ",
		"title": "Trident Emblem"
	},
	{
		"emj": "๐",
		"title": "Name Badge"
	},
	{
		"emj": "๐ฐ",
		"title": "Japanese Symbol For Beginner"
	},
	{
		"emj": "โญ",
		"title": "Heavy Large Circle"
	},
	{
		"emj": "โ",
		"title": "White Heavy Check Mark"
	},
	{
		"emj": "โ",
		"title": "Ballot Box With Check"
	},
	{
		"emj": "โ",
		"title": "Heavy Check Mark"
	},
	{
		"emj": "โ",
		"title": "Heavy Multiplication X"
	},
	{
		"emj": "โ",
		"title": "Cross Mark"
	},
	{
		"emj": "โ",
		"title": "Cross Mark Button"
	},
	{
		"emj": "โ",
		"title": "Heavy Plus Sign"
	},
	{
		"emj": "โ",
		"title": "Heavy Minus Sign"
	},
	{
		"emj": "โ",
		"title": "Heavy Division Sign"
	},
	{
		"emj": "โฐ",
		"title": "Curly Loop"
	},
	{
		"emj": "โฟ",
		"title": "Double Curly Loop"
	},
	{
		"emj": "ใฝ",
		"title": "Part Alternation Mark"
	},
	{
		"emj": "โณ",
		"title": "Eight-spoked Asterisk"
	},
	{
		"emj": "โด",
		"title": "Eight-pointed Star"
	},
	{
		"emj": "โ",
		"title": "Sparkle"
	},
	{
		"emj": "โผ",
		"title": "Double Exclamation Mark"
	},
	{
		"emj": "โ",
		"title": "Exclamation Question Mark"
	},
	{
		"emj": "โ",
		"title": "Question Mark"
	},
	{
		"emj": "โ",
		"title": "White Question Mark"
	},
	{
		"emj": "โ",
		"title": "White Exclamation Mark"
	},
	{
		"emj": "โ",
		"title": "Exclamation Mark"
	},
	{
		"emj": "ใฐ",
		"title": "Wavy Dash"
	},
	{
		"emj": "ยฉ",
		"title": "Copyright"
	},
	{
		"emj": "ยฎ",
		"title": "Registered"
	},
	{
		"emj": "โข",
		"title": "Trade Mark"
	},
	{
		"emj": "#๏ธโฃ",
		"title": "Keycap_#"
	},
	{
		"emj": "*๏ธโฃ",
		"title": "Keycap_*"
	},
	{
		"emj": "0๏ธโฃ",
		"title": "Keycap_0"
	},
	{
		"emj": "1๏ธโฃ",
		"title": "Keycap_1"
	},
	{
		"emj": "2๏ธโฃ",
		"title": "Keycap_2"
	},
	{
		"emj": "3๏ธโฃ",
		"title": "Keycap_3"
	},
	{
		"emj": "4๏ธโฃ",
		"title": "Keycap_4"
	},
	{
		"emj": "5๏ธโฃ",
		"title": "Keycap_5"
	},
	{
		"emj": "6๏ธโฃ",
		"title": "Keycap_6"
	},
	{
		"emj": "7๏ธโฃ",
		"title": "Keycap_7"
	},
	{
		"emj": "8๏ธโฃ",
		"title": "Keycap_8"
	},
	{
		"emj": "9๏ธโฃ",
		"title": "Keycap_9"
	},
	{
		"emj": "๐",
		"title": "Keycap_10"
	},
	{
		"emj": "๐ฏ",
		"title": "Hundred Points"
	},
	{
		"emj": "๐?",
		"title": "Input Latin Uppercase"
	},
	{
		"emj": "๐ก",
		"title": "Input Latin Lowercase"
	},
	{
		"emj": "๐ข",
		"title": "Input Numbers"
	},
	{
		"emj": "๐ฃ",
		"title": "Input Symbols"
	},
	{
		"emj": "๐ค",
		"title": "Input Latin Letters"
	},
	{
		"emj": "๐ฐ",
		"title": "A Button (blood Type)"
	},
	{
		"emj": "๐",
		"title": "AB Button (blood Type)"
	},
	{
		"emj": "๐ฑ",
		"title": "B Button (blood Type)"
	},
	{
		"emj": "๐",
		"title": "CL Button"
	},
	{
		"emj": "๐",
		"title": "COOL Button"
	},
	{
		"emj": "๐",
		"title": "FREE Button"
	},
	{
		"emj": "โน",
		"title": "Information"
	},
	{
		"emj": "๐",
		"title": "ID Button"
	},
	{
		"emj": "โ",
		"title": "Circled M"
	},
	{
		"emj": "๐",
		"title": "NEW Button"
	},
	{
		"emj": "๐",
		"title": "NG Button"
	},
	{
		"emj": "๐พ",
		"title": "O Button (blood Type)"
	},
	{
		"emj": "๐",
		"title": "OK Button"
	},
	{
		"emj": "๐ฟ",
		"title": "P Button"
	},
	{
		"emj": "๐",
		"title": "SOS Button"
	},
	{
		"emj": "๐",
		"title": "UP! Button"
	},
	{
		"emj": "๐",
		"title": "VS Button"
	},
	{
		"emj": "๐",
		"title": "Japanese Here Button"
	},
	{
		"emj": "๐",
		"title": "Japanese Service Charge Button"
	},
	{
		"emj": "๐ท",
		"title": "Japanese Monthly Amount Button"
	},
	{
		"emj": "๐ถ",
		"title": "Japanese Not Free Of Charge Button"
	},
	{
		"emj": "๐ฏ",
		"title": "Japanese Reserved Button"
	},
	{
		"emj": "๐",
		"title": "Japanese Bargain Button"
	},
	{
		"emj": "๐น",
		"title": "Japanese Discount Button"
	},
	{
		"emj": "๐",
		"title": "Japanese Free Of Charge Button"
	},
	{
		"emj": "๐ฒ",
		"title": "Japanese Prohibited Button"
	},
	{
		"emj": "๐",
		"title": "Japanese Acceptable Button"
	},
	{
		"emj": "๐ธ",
		"title": "Japanese Application Button"
	},
	{
		"emj": "๐ด",
		"title": "Japanese Passing Grade Button"
	},
	{
		"emj": "๐ณ",
		"title": "Japanese Vacancy Button"
	},
	{
		"emj": "ใ",
		"title": "Japanese Congratulations Button"
	},
	{
		"emj": "ใ",
		"title": "Japanese Secret Button"
	},
	{
		"emj": "๐บ",
		"title": "Japanese Open For Business Button"
	},
	{
		"emj": "๐ต",
		"title": "Japanese No Vacancy Button"
	},
	{
		"emj": "โช",
		"title": "Black Small Square"
	},
	{
		"emj": "โซ",
		"title": "White Small Square"
	},
	{
		"emj": "โป",
		"title": "White Medium Square"
	},
	{
		"emj": "โผ",
		"title": "Black Medium Square"
	},
	{
		"emj": "โฝ",
		"title": "White Medium-small Square"
	},
	{
		"emj": "โพ",
		"title": "Black Medium-small Square"
	},
	{
		"emj": "โฌ",
		"title": "Black Large Square"
	},
	{
		"emj": "โฌ",
		"title": "White Large Square"
	},
	{
		"emj": "๐ถ",
		"title": "Large Orange Diamond"
	},
	{
		"emj": "๐ท",
		"title": "Large Blue Diamond"
	},
	{
		"emj": "๐ธ",
		"title": "Small Orange Diamond"
	},
	{
		"emj": "๐น",
		"title": "Small Blue Diamond"
	},
	{
		"emj": "๐บ",
		"title": "Red Triangle Pointed Up"
	},
	{
		"emj": "๐ป",
		"title": "Red Triangle Pointed Down"
	},
	{
		"emj": "๐?",
		"title": "Diamond With A Dot"
	},
	{
		"emj": "๐",
		"title": "Radio Button"
	},
	{
		"emj": "๐ฒ",
		"title": "Black Square Button"
	},
	{
		"emj": "๐ณ",
		"title": "White Square Button"
	},
	{
		"emj": "โช",
		"title": "White Circle"
	},
	{
		"emj": "โซ",
		"title": "Black Circle"
	},
	{
		"emj": "๐ด",
		"title": "Red Circle"
	},
	{
		"emj": "๐ต",
		"title": "Blue Circle"
	}
	]
}
];


(function (factory) {
	if (typeof define === 'function' && define.amd) {
		define(['jquery'], factory);
	} else if (typeof exports === 'object') {
		module.exports = factory(require('jquery'));
	} else {
		factory(jQuery);
	}
}(function (e) {
	function convert(textString) {

		textString = textString.replace(/ /g, "-");
		textString = textString.replace(/</g, "");
		textString = textString.replace(/>/g, "");
		textString = textString.replace(/"/g, "");
		textString = textString.replace(/รฉ/g, "");
		textString = textString.replace(/!/g, "");
		textString = textString.replace(/'/, "");
		textString = textString.replace(/ยฃ/, "");
		textString = textString.replace(/^/, "");
		textString = textString.replace(/#/, "");
		textString = textString.replace(/$/, "");
		textString = textString.replace(/\+/g, "");
		textString = textString.replace(/%/g, "");
		textString = textString.replace(/ยฝ/g, "");
		textString = textString.replace(/&/g, "");
		textString = textString.replace(/\//g, "");
		textString = textString.replace(/{/g, "");
		textString = textString.replace(/\(/g, "");
		textString = textString.replace(/\[/g, "");
		textString = textString.replace(/\)/g, "");
		textString = textString.replace(/]/g, "");
		textString = textString.replace(/=/g, "");
		textString = textString.replace(/}/g, "");
		textString = textString.replace(/\?/g, "");
		textString = textString.replace(/\*/g, "");
		textString = textString.replace(/@/g, "");
		textString = textString.replace(/โฌ/g, "");
		textString = textString.replace(/~/g, "");
		textString = textString.replace(/รฆ/g, "");
		textString = textString.replace(/ร/g, "");
		textString = textString.replace(/;/g, "");
		textString = textString.replace(/,/g, "");
		textString = textString.replace(/`/g, "");
		textString = textString.replace(/|/g, "");
		textString = textString.replace(/\./g, "");
		textString = textString.replace(/:/g, "");
		textString = textString.replace(/ฤฐ/g, "i");
		textString = textString.replace(/I/g, "i");
		textString = textString.replace(/ฤฑ/g, "i");
		textString = textString.replace(/ฤ/g, "g");
		textString = textString.replace(/ฤ/g, "g");
		textString = textString.replace(/รผ/g, "u");
		textString = textString.replace(/ร/g, "u");
		textString = textString.replace(/ล/g, "s");
		textString = textString.replace(/ล/g, "s");
		textString = textString.replace(/รถ/g, "o");
		textString = textString.replace(/ร/g, "o");
		textString = textString.replace(/รง/g, "c");
		textString = textString.replace(/ร/g, "c");
		textString = textString.replace(/--/g, "-");
		textString = textString.replace(/---/g, "-");
		textString = textString.replace(/----/g, "-");
		textString = textString.replace(/----/g, "-");

		return textString.toLowerCase();
	}
	var C = {
		main: ".ms-emoji-main",
		header: ".ms-emoji-header",
		content: ".ms-emoji-content",
		list: ".ms-emoji-wk",
		title: ".ms-emoji-title",
		listemoji: ".ms-emoji-list",
		emoji: ".ms-emoji-button"
	},
	H = {
		main: '<div class="'+C.main.replace(".", "")+'"></div>',
		header: '<div class="'+C.header.replace(".", "")+'"></div>',
		content: '<div class="'+C.content.replace(".", "")+'"></div>',
		list: '<div class="'+C.list.replace(".", "")+'"></div>',
		title: '<div class="'+C.title.replace(".", "")+'">{title}</div>',
		listemoji: '<div class="'+C.listemoji.replace(".", "")+'" data-name="{dataTitle}"></div>',
		emoji: '<button class="'+C.emoji.replace(".", "")+'" onclick="selectEmoji('+"'{emoji}'"+')" data-title="{title}">{emojiAlt}</button>',
	},
	F = {
		A: function(lang){
			/*e.cachedScript = function(url, options ) {
				options = $.extend( options || {}, {
					dataType: "script",
					cache: true,
					url: url
				});
				return e.ajax( options )
			};
			$.cachedScript( "msEmoji."+lang+".js?v=1.0.2" ).done(function(emojies) {
				F.E(msEmo, input);
			})*/F.E(msEmo, input);
		},
		H:function(emj){
			e(C.header).append('<div class="ms-emoji-hed-action"></div>');
			e(".ms-emoji-hed-action").append('<div class="ms-emoji-search"><input type="search" placeholder="'+input+'" class="ms-emoji-search"></div>');
			e(".ms-emoji-hed-action").append('<ul class="ms-emoji-action"><li class="ms-s65" onclick="showEmoji(0)"><svg viewBox="0 0 24 24" width="34px" height="34px" x="0" y="0"><path d="M13,4.32L9.31,8,13,11.69,11.69,13,8,9.31,4.31,13,3,11.69,6.69,8,3,4.31,4.31,3,8,6.69,11.68,3Z" class="ms-emoji-close"></path></svg></li></ul>');
			
			e(C.main).append('<ul class="ms-emoji-head"></ul>');
			
		},
		E: function(emojies, input){
			if(emojies.length > 0){
				this.mainClass.append(H.main);
				e(C.main).append(H.header);
				e(C.main).append(H.content);
				e(C.content).append(H.list);
				F.H(input);
				F.Title(emojies);
			}
		},
		Title:function(emoji){
			var em = "";
			$.each(emoji, function(i, emj) {
				em += H.title.replace('{title}', emj.name);
				e(C.list).append(H.title.replace('{title}', emj.name));
				e(C.list).append(H.listemoji.replace('{dataTitle}', convert(emj.name)));
				F.Emoji("[data-name="+convert(emj.name)+"]", emj.emojies)
				e("ul.ms-emoji-head").append(
					'<li class="ms-emoji-head"><button class="ms-emoji-group" data-title="'+convert(emj.name)+'">'+emj.emojies[0].emj+'</button></li>'
					);
				console.log(  )
			})
		},
		Emoji: function(eBlock, emoji){
			e.each(emoji, function(i, moji) {
				e(eBlock).append(
					H.emoji
					.replace('{title}', moji.title)
					.replace('{emoji}', moji.emj)
					.replace('{emojiAlt}', moji.emj) 
					)
			})
		},
		Search: function(){
			e(document).on("keyup", 'input.ms-emoji-search', function(){
				var s = convert(e(this).val());
				console.log(s)
				e('.ms-emoji-button').each(function(){
					var t = convert(e(this).data("title")),
					l = t.indexOf(s) !== -1;
					e(this).toggle(l);
				})     
			})
		},
		Click: function(){
			e(document).on('click', 'button.ms-emoji-group', function(ev) {
				ev.preventDefault(); 

				var h = 0;
				e('.ms-emoji-wk > div').each(function(index, el) {
					h += e(this).height()
				})

				var 
				t = e(this).data("title"),
				tt = e('[data-name="'+t+'"]'),
				s = tt.offset().top,
				eh = tt.height();


				console.log(  s , e('.ms-emoji-wk').scrollTop()  )
				
				e('.ms-emoji-wk').animate({
					scrollTop: s - 85
				});




				/*var t = e(this).data("title");
				
				e('.ms-emoji-wk').animate({
					scrollTop: e('[data-name="'+t+'"]').offset().top-90
				});
				
				console.log( 
					t,
					e('[data-name="'+t+'"]').offset() 
					)
					return false;*/
				});
			
		},
		Scroll: function(){
			e('.ms-emoji-wk').scroll( function() {
				var scrollDistance = e(this).scrollTop();
				$('.ms-emoji-list').each(function(i) {
					if (e(this).position().top <= scrollDistance) {
						e('.navigation a.active').removeClass('active');
						e('.navigation a').eq(i).addClass('active');
					}

				});
			}).scroll();
		},
		Init: function(mainClass, o){ 
			this.lang = o.lang;
			this.input = o.input;
			this.mainClass = mainClass
			F.A(this.lang);
			F.Click();
			F.Scroll();
			F.Search();
		}
	};


	$.fn.msEmoji = function(o) {
		try {
			F.Init( $(this), o )
		} catch(e) {}
	};
}));


(function($){
    $.fn.focusTextToEnd = function() {
        $initialVal = this.val();
        this.val($initialVal + ' ');
        this.val($initialVal);
    };
})(jQuery);

