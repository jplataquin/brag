<?php
$content = file_get_contents('app/Http/Controllers/BattleActionController.php');

$old = <<<'PHP'
    public function updateTeamName(Request $request, Battle $battle)
    {
        $request->validate([
            'team' => 'required|in:A,B',
            'name' => 'required|string|max:50',
        ]);
PHP;

$new = <<<'PHP'
    public function updateTeamName(Request $request, Battle $battle)
    {
        if ($battle->status !== 'pending') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Team names can only be changed while the battle is pending.'], 403);
            }
            return back()->with('error', 'Team names can only be changed while the battle is pending.');
        }

        $request->validate([
            'team' => 'required|in:A,B',
            'name' => 'required|string|max:50',
        ]);
PHP;

$content = str_replace($old, $new, $content);
file_put_contents('app/Http/Controllers/BattleActionController.php', $content);
