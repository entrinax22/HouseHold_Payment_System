<template>
    <nav class="border-b bg-background px-8 py-3 flex items-center justify-between">
        <div class="flex items-center">
            <a href="#" class="font-semibold text-lg tracking-tight text-primary">Household Payment System</a>
        </div>
        <div>
            <ul class="flex gap-6">
                <li>
                    <a href="/dashboard" class="transition-colors text-muted-foreground hover:text-primary font-medium">
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="/payments" class="transition-colors text-muted-foreground hover:text-primary font-medium">
                        Payments
                    </a>
                </li>
                <li>
                    <a href="#" class="transition-colors text-muted-foreground hover:text-primary font-medium">
                        Reports
                    </a>
                </li>
                <li>
                    <a href="#" class="transition-colors text-muted-foreground hover:text-primary font-medium">
                        Settings
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</template>

<script>
export default {
    name: 'Navbar'
}
</script>
