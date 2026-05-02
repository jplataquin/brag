#!/bin/bash

# 1. Add cards.heal
sed -i 's/Route::get('\''\/inventory'\'', \[DigitalCardController::class, '\''index'\''\])->name('\''cards.index'\'');/Route::get('\''\/inventory'\'', \[DigitalCardController::class, '\''index'\''\])->name('\''cards.index'\'');\n    Route::post('\''\/cards\/{card}\/heal'\'', \[DigitalCardController::class, '\''heal'\''\])->name('\''cards.heal'\'');/g' routes/web.php

# 2. Remove battles.store
sed -i '/Route::post('\''\/battles'\'', \[BattleController::class, '\''store'\''\])->name('\''battles.store'\'');/d' routes/web.php

# 3. Add search inside Profiles
sed -i 's/Route::get('\''\/profile\/{username}'\'', \[ProfileController::class, '\''show'\''\])->name('\''profile.show'\'');/Route::get('\''\/search'\'', \[ProfileController::class, '\''search'\''\])->name('\''search'\'');\n    Route::get('\''\/profile\/{username}'\'', \[ProfileController::class, '\''show'\''\])->name('\''profile.show'\'');/g' routes/web.php

# 4. Remove profile.destroy
sed -i '/Route::delete('\''\/profile'\'', \[ProfileController::class, '\''destroy'\''\])->name('\''profile.destroy'\'');/d' routes/web.php

# 5. Remove payments.create and payments.failure
sed -i '/Route::get('\''\/diamonds\/purchase'\'', \[PaymentController::class, '\''create'\''\])->name('\''payments.create'\'');/d' routes/web.php
sed -i '/Route::get('\''\/payments\/failure'\'', \[PaymentController::class, '\''failure'\''\])->name('\''payments.failure'\'');/d' routes/web.php

# 6. Except show on Game Titles
sed -i 's/Route::resource('\''game_titles'\'', AdminGameTitleController::class);/Route::resource('\''game_titles'\'', AdminGameTitleController::class)->except(\['\''show'\''\]);/g' routes/web.php

# 7. Add maintenance route
sed -i 's/\/\/ Static Pages/\/\/ Static Pages\nRoute::view('\''\/maintenance'\'', '\''maintenance'\'')->name('\''maintenance'\'');/g' routes/web.php

# 8. Fix cards.forge in views
sed -i 's/route('\''cards.forge'\''/route('\''templates.forge'\''/g' resources/views/templates/show.blade.php
