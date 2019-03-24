{if $priority == "Trivial"}
    <p class="text-muted" title="Trivial"><i class="fas fa-arrow-down"></i></p>
{elseif $priority == "Minor"}
    <p class="text-success" title="Minor"><i class="fas fa-angle-double-down"></i></p>
{elseif $priority == "Major"}
    <p class="text-danger" title="Major"><i class="fas fa-angle-double-up"></i></p>
{elseif $priority == "Critical"}
    <p class="text-danger" title="Critical"><i class="fas fa-angle-up"></i></p>
{elseif $priority == "Blocker"}
    <p class="text-danger" title="Blocker"><i class="fas fa-ban"></i></p>
{/if}
