import ast, json, sys

#grab fucntion name and json from backend. Passed in as arguments
try:
	fucntionName=sys.argv[1]
	inputs=sys.argv[2]

	j=ast.literal_eval(inputs)
except Exception as e:
	#print(e)
	print("Error getting arguments.")

#read student file
with open('ans.py', 'r') as source:
    r=source.read()
    syntax=ast.parse(r, mode='exec')

class ArgAssign(ast.NodeTransformer):
    def __init__(self, modList):
        self.ag = modList

    def visit_Module(self, node):
        return ast.Module(node.body + [ast.Assign(targets=[ast.Name(id='ans', ctx=ast.Store())], value=ast.Call(func=ast.Name(id=fucntionName, ctx=ast.Load()), args=self.ag, keywords=[]), type_comment=None), ast.If(test=ast.Name(id='ans', ctx=ast.Load()), body=[ast.Expr(value=ast.Call(func=ast.Name(id='print', ctx=ast.Load()), args=[ast.Name(id='ans', ctx=ast.Load())], keywords=[]))], orelse=[])])

def default_func():
	testcase=[]
	for m in j:
		if type(m) == str:
			testcase_ast=ast.parse('\"{0}\"'.format(m))
		else:
			testcase_ast=ast.parse('{0}'.format(m))

		testcase+=[testcase_ast.body[0].value]

	mod_ast=ArgAssign(testcase).visit(syntax)
	post=compile(ast.fix_missing_locations(mod_ast),'<string>', mode='exec')
	exec(post)

try:
	default_func()
except Exception as e2:
	#print(e2)
	print("Error executing function body.")

